import math
import os
import torch
import torch.nn as nn
import torch.optim as optim
from torch.utils.data import Dataset, DataLoader
import random
import numpy as np
from sklearn.metrics.pairwise import cosine_similarity
from sklearn.feature_extraction.text import TfidfVectorizer
from collections import Counter
import ast
import warnings
from tqdm import tqdm
from functools import partial  # Added import

# ===============================
# 1. Suppress Specific Warnings
# ===============================
warnings.filterwarnings(
    "ignore",
    category=UserWarning,
    message="Support for mismatched key_padding_mask and attn_mask is deprecated.*",
)

warnings.filterwarnings(
    "ignore",
    category=FutureWarning,
    message=".*torch.load.*",
)

# ===============================
# 2. Set Random Seeds
# ===============================
torch.manual_seed(42)
np.random.seed(42)
random.seed(42)

# ===============================
# 3. Tokenization
# ===============================
def tokenize(text):
    return text.strip().split()

# ===============================
# 4. Vocabulary
# ===============================
def build_vocab(conversations, min_freq=1, max_size=None):
    counter = Counter()
    for question, answer in conversations:
        counter.update(tokenize(question))
        counter.update(tokenize(answer))
    specials = ['<pad>', '<unk>', '<bos>', '<eos>']
    vocab = {token: idx for idx, token in enumerate(specials)}
    idx2word = {idx: token for idx, token in enumerate(specials)}

    sorted_tokens = sorted(
        [item for item in counter.items() if item[0] not in specials and item[1] >= min_freq],
        key=lambda x: x[1],
        reverse=True
    )
    if max_size:
        sorted_tokens = sorted_tokens[:max_size - len(specials)]

    for token, freq in sorted_tokens:
        if token not in vocab:
            idx = len(vocab)
            vocab[token] = idx
            idx2word[idx] = token

    return vocab, idx2word

# ===============================
# 5. Dataset
# ===============================
class ConversationDataset(Dataset):
    def __init__(self, dataset_path, src_vocab, tgt_vocab, max_length=50, delimiter='\t'):
        self.data = []
        self.src_vocab = src_vocab
        self.tgt_vocab = tgt_vocab
        self.max_length = max_length
        self.delimiter = delimiter
        self.string_data = []

        if dataset_path is not None:
            self._load_dataset(dataset_path)

    def _load_dataset(self, dataset_path):
        skipped_lines = 0
        with open(dataset_path, 'r', encoding='utf-8', errors='replace') as f:
            for line_number, line in enumerate(f, 1):
                stripped_line = line.strip()
                if not stripped_line:
                    continue
                # If line ends with a comma, remove it
                if stripped_line.endswith(','):
                    stripped_line = stripped_line[:-1].strip()
                try:
                    parsed = ast.literal_eval(stripped_line)
                    if isinstance(parsed, tuple) and len(parsed) == 2:
                        question, answer = parsed
                        self.data.append((str(question).strip(), str(answer).strip()))
                        self.string_data.append((str(question).strip(), str(answer).strip()))
                    else:
                        skipped_lines += 1
                except:
                    skipped_lines += 1
        print(f"Loaded {len(self.data)} conversation pairs from dataset.")
        if skipped_lines > 0:
            print(f"Skipped {skipped_lines} malformed lines.")

    def __len__(self):
        return len(self.data)

    def __getitem__(self, idx):
        question, answer = self.data[idx]
        input_tokens = tokenize(question)
        target_tokens = tokenize(answer)

        input_ids = [self.src_vocab["<bos>"]] + [self.src_vocab.get(t, self.src_vocab["<unk>"]) for t in input_tokens] + [self.src_vocab["<eos>"]]
        target_ids = [self.tgt_vocab["<bos>"]] + [self.tgt_vocab.get(t, self.tgt_vocab["<unk>"]) for t in target_tokens] + [self.tgt_vocab["<eos>"]]

        input_ids = input_ids[:self.max_length]
        target_ids = target_ids[:self.max_length]

        return {
            'input_ids': torch.tensor(input_ids, dtype=torch.long),
            'target_ids': torch.tensor(target_ids, dtype=torch.long),
            'input_text': question,
            'target_text': answer
        }

# ===============================
# 6. Collate
# ===============================
def collate_fn(batch, pad_idx):
    input_ids = [item['input_ids'] for item in batch]
    target_ids = [item['target_ids'] for item in batch]
    input_texts = [item['input_text'] for item in batch]
    target_texts = [item['target_text'] for item in batch]

    input_padded = nn.utils.rnn.pad_sequence(input_ids, batch_first=True, padding_value=pad_idx)
    target_padded = nn.utils.rnn.pad_sequence(target_ids, batch_first=True, padding_value=pad_idx)
    return input_padded, target_padded, input_texts, target_texts

# ===============================
# 7. Model
# ===============================
class PositionalEncoding(nn.Module):
    def __init__(self, embed_size, dropout=0.1, max_len=5000):
        super(PositionalEncoding, self).__init__()
        self.dropout = nn.Dropout(p=dropout)
        self.embed_size = embed_size
        self.max_len = max_len
        self.register_buffer('pe', self._generate_positional_encoding())

    def _generate_positional_encoding(self):
        pe = torch.zeros(1, self.max_len, self.embed_size)
        position = torch.arange(0, self.max_len, dtype=torch.float32).unsqueeze(1)
        div_term = torch.exp(torch.arange(0, self.embed_size, 2).float() * (-math.log(10000.0) / self.embed_size))
        pe[:, :, 0::2] = torch.sin(position * div_term)
        if self.embed_size % 2 == 1:
            pe[:, :, 1::2] = torch.cos(position * div_term[:-1])
        else:
            pe[:, :, 1::2] = torch.cos(position * div_term)
        return pe

    def forward(self, x):
        x = x + self.pe[:, :x.size(1), :]
        return self.dropout(x)

    def reinitialize(self):
        self.pe = self._generate_positional_encoding().to(self.pe.device)

class TransformerModel(nn.Module):
    def __init__(self, src_vocab_size, tgt_vocab_size, d_model=256, nhead=8, num_encoder_layers=6, num_decoder_layers=6, dim_feedforward=512, dropout=0.1):
        super(TransformerModel, self).__init__()
        self.src_embedding = nn.Embedding(src_vocab_size, d_model)
        self.tgt_embedding = nn.Embedding(tgt_vocab_size, d_model)
        self.pos_encoder = PositionalEncoding(d_model, dropout)
        self.transformer = nn.Transformer(
            d_model=d_model,
            nhead=nhead,
            num_encoder_layers=num_encoder_layers,
            num_decoder_layers=num_decoder_layers,
            dim_feedforward=dim_feedforward,
            dropout=dropout,
            batch_first=True
        )
        self.fc_out = nn.Linear(d_model, tgt_vocab_size)
        self.d_model = d_model
        self._reset_parameters()

    def _reset_parameters(self):
        for p in self.parameters():
            if p.dim() > 1:
                nn.init.xavier_uniform_(p)

    def forward(self, src, tgt, src_mask, tgt_mask, src_padding_mask, tgt_padding_mask, memory_key_padding_mask):
        src_emb = self.pos_encoder(self.src_embedding(src) * math.sqrt(self.d_model))
        tgt_emb = self.pos_encoder(self.tgt_embedding(tgt) * math.sqrt(self.d_model))

        memory = self.transformer.encoder(src_emb, src_key_padding_mask=src_padding_mask)
        output = self.transformer.decoder(tgt_emb, memory, tgt_mask=tgt_mask,
                                          memory_key_padding_mask=memory_key_padding_mask,
                                          tgt_key_padding_mask=tgt_padding_mask)
        return self.fc_out(output)

    def generate_batch(self, src_sentences, src_vocab, tgt_vocab, tgt_idx2word, device, max_len=50, temperature=1.0):
        self.eval()
        with torch.no_grad():
            batch_size = len(src_sentences)
            input_ids = []
            for sentence in src_sentences:
                tokens = [src_vocab.get("<bos>")] + [src_vocab.get(t, src_vocab["<unk>"]) for t in tokenize(sentence)] + [src_vocab.get("<eos>")]
                tokens = tokens[:max_len]
                input_ids.append(tokens)

            input_padded = nn.utils.rnn.pad_sequence(
                [torch.tensor(ids, dtype=torch.long, device=device) for ids in input_ids],
                batch_first=True, padding_value=src_vocab["<pad>"]
            )
            src_padding_mask = (input_padded == src_vocab["<pad>"])

            src_emb = self.pos_encoder(self.src_embedding(input_padded) * math.sqrt(self.d_model))
            memory = self.transformer.encoder(src_emb, src_key_padding_mask=src_padding_mask)

            generated_ids = torch.ones(batch_size, 1, dtype=torch.long, device=device) * tgt_vocab["<bos>"]

            for step in range(max_len - 1):
                gen_seq_len = generated_ids.size(1)
                if gen_seq_len < 1:
                    break
                tgt_mask = nn.Transformer.generate_square_subsequent_mask(gen_seq_len).to(device)

                tgt_emb = self.pos_encoder(self.tgt_embedding(generated_ids) * math.sqrt(self.d_model))
                output = self.transformer.decoder(tgt_emb, memory, tgt_mask=tgt_mask,
                                                  memory_key_padding_mask=src_padding_mask,
                                                  tgt_key_padding_mask=(generated_ids == tgt_vocab["<pad>"]))

                logits = self.fc_out(output)[:, -1, :]
                logits = logits.clamp(-10, 10)

                prob = torch.softmax(logits / temperature, dim=-1)

                if torch.isnan(prob).any() or torch.isinf(prob).any() or (prob < 0).any():
                    next_tokens = torch.argmax(logits, dim=-1)
                else:
                    next_tokens = torch.multinomial(prob, num_samples=1).squeeze(1)

                if (next_tokens == tgt_vocab["<eos>"]).all():
                    break

                generated_ids = torch.cat([generated_ids, next_tokens.unsqueeze(1)], dim=1)

            generated_ids = generated_ids.tolist()
            translated_texts = []
            for gen in generated_ids:
                tokens = [tgt_idx2word.get(token, "<unk>") for token in gen if token not in [tgt_vocab["<bos>"], tgt_vocab["<pad>"], tgt_vocab["<eos>"]]]
                translated_texts.append(" ".join(tokens))

            return translated_texts

# ===============================
# 8. Rewards
# ===============================
def compute_rewards(predicted_responses, reference_responses):
    vectorizer = TfidfVectorizer()
    all_text = predicted_responses + reference_responses
    if not all_text:
        return np.array([0.0])
    tfidf_matrix = vectorizer.fit_transform(all_text)
    pred_vec = tfidf_matrix[:len(predicted_responses)]
    ref_vec = tfidf_matrix[len(predicted_responses):]
    if pred_vec.shape[0] == 0 or ref_vec.shape[0] == 0:
        return np.array([0.0]*len(predicted_responses))
    similarities = cosine_similarity(pred_vec, ref_vec)
    rewards = similarities.diagonal()
    rewards = (rewards + 1) / 2
    return rewards

# ===============================
# 9. Device Verification
# ===============================
def verify_model_device(model, device):
    mismatch = False
    for name, param in model.named_parameters():
        if param.device != device:
            print(f"Parameter {name} is on {param.device}, expected {device}.")
            mismatch = True
    for name, buffer in model.named_buffers():
        if buffer.device != device:
            print(f"Buffer {name} is on {buffer.device}, expected {device}.")
            mismatch = True
    if not mismatch:
        print("All model parameters and buffers are on the correct device.")
    else:
        print("Device mismatch detected in model parameters or buffers.")

# ===============================
# 10. RL Fine-Tuning
# ===============================
def rl_fine_tune(model, dataloader, optimizer, src_vocab, tgt_vocab, src_idx2word, tgt_idx2word, additional_dataloader=None, epochs=3, device='cuda', patience=2, save_model_dir='saved_models_rl'):
    model.train()
    best_reward = -float('inf')
    epochs_no_improve = 0

    for epoch in range(epochs):
        print(f"\nEpoch {epoch + 1}/{epochs}")
        epoch_rewards = []
        total_loss = 0.0
        num_batches = 0

        with tqdm(dataloader, desc=f"Epoch {epoch + 1}", unit="batch", mininterval=1.0, leave=False, position=0) as progress_bar:
            for batch in progress_bar:
                input_padded, target_padded, input_texts, target_texts = batch
                input_padded = input_padded.to(device)
                target_padded = target_padded.to(device)
                batch_size = input_padded.size(0)

                generated_texts = model.generate_batch(
                    input_texts,
                    src_vocab,
                    tgt_vocab,
                    tgt_idx2word,
                    device,
                    max_len=50,
                    temperature=1.0
                )

                rewards = compute_rewards(generated_texts, target_texts)
                rewards_tensor = torch.tensor(rewards, dtype=torch.float32, device=device)

                generated_ids = []
                for text in generated_texts:
                    tokens = tokenize(text)
                    ids = [tgt_vocab["<bos>"]] + [tgt_vocab.get(t, tgt_vocab["<unk>"]) for t in tokens] + [tgt_vocab["<eos>"]]
                    if len(ids) == 0:
                        ids = [tgt_vocab["<bos>"], tgt_vocab["<eos>"]]
                    ids = ids[:50]
                    generated_ids.append(ids)

                if len(generated_ids) == 0:
                    continue
                generated_ids_padded = nn.utils.rnn.pad_sequence(
                    [torch.tensor(ids, dtype=torch.long, device=device) for ids in generated_ids],
                    batch_first=True,
                    padding_value=tgt_vocab["<pad>"]
                )

                gen_seq_len = generated_ids_padded.size(1)
                if gen_seq_len < 1:
                    continue

                tgt_padding_mask = (generated_ids_padded == tgt_vocab["<pad>"])
                tgt_mask = nn.Transformer.generate_square_subsequent_mask(gen_seq_len).to(device)

                logits = model(
                    src=input_padded,
                    tgt=generated_ids_padded,
                    src_mask=None,
                    tgt_mask=tgt_mask,
                    src_padding_mask=(input_padded == src_vocab["<pad>"]),
                    tgt_padding_mask=tgt_padding_mask,
                    memory_key_padding_mask=(input_padded == src_vocab["<pad>"])
                )

                log_probs = torch.log_softmax(logits, dim=-1)
                last_tokens = generated_ids_padded[:, -1]
                log_probs_last = log_probs[range(batch_size), -1, last_tokens]

                loss = -(log_probs_last * rewards_tensor).mean()
                optimizer.zero_grad()
                loss.backward()
                torch.nn.utils.clip_grad_norm_(model.parameters(), max_norm=0.5)
                optimizer.step()

                epoch_rewards.extend(rewards)
                total_loss += loss.item()
                num_batches += 1

                # Update progress bar postfix every 10 batches to reduce update frequency
                if num_batches % 10 == 0:
                    avg_loss = total_loss / num_batches
                    avg_reward = sum(epoch_rewards) / len(epoch_rewards) if epoch_rewards else 0.0
                    progress_bar.set_postfix({'Avg Reward': f"{avg_reward:.4f}", 'Avg Loss': f"{avg_loss:.4f}"})

        if additional_dataloader:
            with tqdm(additional_dataloader, desc=f"Epoch {epoch + 1} - Additional", unit="batch", mininterval=1.0, leave=True, position=0) as additional_bar:
                for batch in additional_bar:
                    input_padded, target_padded, input_texts, target_texts = batch
                    input_padded = input_padded.to(device)
                    target_padded = target_padded.to(device)
                    batch_size = input_padded.size(0)

                    generated_texts = model.generate_batch(
                        input_texts,
                        src_vocab,
                        tgt_vocab,
                        tgt_idx2word,
                        device,
                        max_len=50,
                        temperature=1.0
                    )

                    rewards = compute_rewards(generated_texts, target_texts)
                    rewards_tensor = torch.tensor(rewards, dtype=torch.float32, device=device)

                    generated_ids = []
                    for text in generated_texts:
                        tokens = tokenize(text)
                        ids = [tgt_vocab["<bos>"]] + [tgt_vocab.get(t, tgt_vocab["<unk>"]) for t in tokens] + [tgt_vocab["<eos>"]]
                        if len(ids) == 0:
                            ids = [tgt_vocab["<bos>"], tgt_vocab["<eos>"]]
                        ids = ids[:50]
                        generated_ids.append(ids)

                    if len(generated_ids) == 0:
                        continue
                    generated_ids_padded = nn.utils.rnn.pad_sequence(
                        [torch.tensor(ids, dtype=torch.long, device=device) for ids in generated_ids],
                        batch_first=True,
                        padding_value=tgt_vocab["<pad>"]
                    )
                    gen_seq_len = generated_ids_padded.size(1)
                    if gen_seq_len < 1:
                        continue

                    tgt_padding_mask = (generated_ids_padded == tgt_vocab["<pad>"])
                    tgt_mask = nn.Transformer.generate_square_subsequent_mask(gen_seq_len).to(device)

                    logits = model(
                        src=input_padded,
                        tgt=generated_ids_padded,
                        src_mask=None,
                        tgt_mask=tgt_mask,
                        src_padding_mask=(input_padded == src_vocab["<pad>"]),
                        tgt_padding_mask=tgt_padding_mask,
                        memory_key_padding_mask=(input_padded == src_vocab["<pad>"])
                    )

                    log_probs = torch.log_softmax(logits, dim=-1)
                    last_tokens = generated_ids_padded[:, -1]
                    log_probs_last = log_probs[range(batch_size), -1, last_tokens]
                    loss = -(log_probs_last * rewards_tensor).mean()
                    optimizer.zero_grad()
                    loss.backward()
                    torch.nn.utils.clip_grad_norm_(model.parameters(), max_norm=0.5)
                    optimizer.step()

                    epoch_rewards.extend(rewards)
                    total_loss += loss.item()
                    num_batches += 1

                    # Update progress bar postfix every 10 batches
                    if num_batches % 10 == 0:
                        avg_loss = total_loss / num_batches
                        avg_reward = sum(epoch_rewards) / len(epoch_rewards) if epoch_rewards else 0.0
                        additional_bar.set_postfix({'Avg Reward': f"{avg_reward:.4f}", 'Avg Loss': f"{avg_loss:.4f}"})

        avg_reward = sum(epoch_rewards) / len(epoch_rewards) if epoch_rewards else 0.0
        avg_loss = total_loss / num_batches if num_batches > 0 else 0.0
        print(f"Average Reward for Epoch {epoch + 1}: {avg_reward:.4f}, Average Loss: {avg_loss:.4f}")

        if avg_reward > best_reward:
            best_reward = avg_reward
            epochs_no_improve = 0
            os.makedirs(save_model_dir, exist_ok=True)
            torch.save(model.state_dict(), os.path.join(save_model_dir, 'best_model_rl.pth'))
            print("Best model updated.")
        else:
            epochs_no_improve += 1
            print(f"No improvement in reward for {epochs_no_improve} epoch(s).")
            if epochs_no_improve >= patience:
                print("Early stopping triggered.")
                break

# ===============================
# 11. Main
# ===============================
def main():
    dataset_file = 'extra_conversations.txt'  # your file with tuple-format lines
    model_file = 'AllOneLM.pth'
    save_model_dir = 'saved_models_rl'
    embed_size = 256
    num_heads = 8
    hidden_dim = 512
    num_layers = 6
    dropout = 0.1
    epochs = 10
    batch_size = 2
    max_length = 50
    patience = 3
    delimiter = '\t'

    # Load additional examples from tuple-formatted lines
    additional_examples = []
    if os.path.exists(dataset_file):
        with open(dataset_file, "r", encoding="utf-8") as f:
            for line in f:
                line = line.strip()
                if not line:
                    continue
                # Remove trailing comma if exists
                if line.endswith(','):
                    line = line[:-1].strip()
                try:
                    pair = ast.literal_eval(line)
                    if isinstance(pair, tuple) and len(pair) == 2:
                        question, answer = pair
                        additional_examples.append((question.strip(), answer.strip()))
                except:
                    pass
        print(f"Loaded {len(additional_examples)} conversation pairs from file.")
    else:
        print(f"Dataset file {dataset_file} not found. Proceeding without additional examples.")

    print("Building vocabulary from additional examples...")
    temp_conversations = []
    temp_conversations.extend(additional_examples)
    print(f"Loaded {len(temp_conversations)} conversation pairs from additional examples.")

    src_vocab, src_idx2word = build_vocab(temp_conversations, min_freq=1, max_size=10000)
    tgt_vocab, tgt_idx2word = build_vocab(temp_conversations, min_freq=1, max_size=10000)
    print(f"Source Vocabulary Size: {len(src_vocab)}")
    print(f"Target Vocabulary Size: {len(tgt_vocab)}")

    if additional_examples:
        print("Creating dataset and dataloader for additional examples...")
        additional_dataset = ConversationDataset(
            dataset_path=None,
            src_vocab=src_vocab,
            tgt_vocab=tgt_vocab,
            max_length=max_length,
            delimiter=delimiter
        )
        additional_dataset.data = additional_examples
        additional_dataset.string_data = additional_examples

        # Create a picklable collate function using functools.partial
        collate = partial(collate_fn, pad_idx=src_vocab["<pad>"])

        additional_dataloader = DataLoader(
            additional_dataset,
            batch_size=batch_size,
            shuffle=True,
            collate_fn=collate,  # Use the partial function here
            num_workers=5,       # You can reduce this if issues persist
            pin_memory=True
        )
        print(f"Loaded {len(additional_dataloader.dataset)} additional conversation pairs for RL.")
    else:
        additional_dataloader = None
        print("No additional examples provided.")

    device = torch.device('cuda:0' if torch.cuda.is_available() else 'cpu')
    vocab_size_src = len(src_vocab)
    vocab_size_tgt = len(tgt_vocab)
    model = TransformerModel(
        src_vocab_size=vocab_size_src,
        tgt_vocab_size=vocab_size_tgt,
        d_model=embed_size,
        nhead=num_heads,
        num_encoder_layers=num_layers,
        num_decoder_layers=num_layers,
        dim_feedforward=hidden_dim,
        dropout=dropout
    ).to(device)

    if model_file and os.path.exists(model_file):
        try:
            state_dict = torch.load(model_file, map_location=device)
            pos_encoder_key = 'pos_encoder.pe'
            if pos_encoder_key in state_dict and state_dict[pos_encoder_key].shape[0] == 5000:
                if state_dict[pos_encoder_key].dim() == 3 and state_dict[pos_encoder_key].shape[1] == 1:
                    print(f"Adjusting {pos_encoder_key} dimensions from {state_dict[pos_encoder_key].shape} to [1, 5000, {embed_size}]")
                    state_dict[pos_encoder_key] = state_dict[pos_encoder_key].permute(1, 0, 2)

            excluded_keys = ['src_embedding.weight', 'tgt_embedding.weight', 'fc_out.weight', 'fc_out.bias']
            for key in excluded_keys:
                if key in state_dict:
                    del state_dict[key]
                    print(f"Excluded {key} from state_dict due to size mismatch.")
            model.load_state_dict(state_dict, strict=False)
            print(f"Loaded model weights from {model_file} with strict=False.")

            nn.init.xavier_uniform_(model.src_embedding.weight)
            print("Reinitialized src_embedding.weight.")
            nn.init.xavier_uniform_(model.tgt_embedding.weight)
            print("Reinitialized tgt_embedding.weight.")
            nn.init.xavier_uniform_(model.fc_out.weight)
            print("Reinitialized fc_out.weight.")
            nn.init.zeros_(model.fc_out.bias)
            print("Reinitialized fc_out.bias.")

            nan_params = [n for n, p in model.named_parameters() if torch.isnan(p).any()]
            inf_params = [n for n, p in model.named_parameters() if torch.isinf(p).any()]

            if nan_params or inf_params:
                print("Model parameters contain NaN or Inf after loading.")
                return
            else:
                print("All model parameters are finite.")

        except RuntimeError as e:
            print(f"RuntimeError while loading state_dict: {e}")
            return
    else:
        print(f"Model file {model_file} not found. Exiting.")
        return

    model.pos_encoder.reinitialize()
    print("Positional Encoding reinitialized.")

    verify_model_device(model, device)

    optimizer = optim.Adam(model.parameters(), lr=1e-6)

    try:
        if additional_dataloader is not None:
            rl_fine_tune(
                model,
                additional_dataloader,
                optimizer,
                src_vocab,
                tgt_vocab,
                src_idx2word,
                tgt_idx2word,
                additional_dataloader=None,
                epochs=epochs,
                device=device,
                patience=patience,
                save_model_dir=save_model_dir
            )
        else:
            print("No datasets available for RL fine-tuning.")
    except RuntimeError as e:
        print(f"Anomaly detected during training: {e}")
        print("Investigate the source of NaN or Inf values.")

    os.makedirs(save_model_dir, exist_ok=True)
    model_save_path = os.path.join(save_model_dir, 'fine_tuned_transformer_rl.pth')
    torch.save(model.state_dict(), model_save_path)
    print(f"Fine-tuned model saved to {model_save_path}.")
    print("\nRL Fine-Tuning completed.")


if __name__ == '__main__':
    torch.cuda.empty_cache()
    main()
