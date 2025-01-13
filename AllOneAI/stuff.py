from collections import Counter

with open("text.txt", "r") as f:
    text = f.read()
words = text.lower().split()

textinput = input("Enter text:")

currenttext = []
des = 9

def initial():
    words_after_like = []
    for i in range(len(words) - 1):
        if words[i] == textinput:
            words_after_like.append(words[i+1])

    word_counts = Counter(words_after_like)
    total_words = len(words_after_like)
    print("recorded")
    if total_words > 0:
        sorted_counts = sorted(word_counts.items(), key=lambda item: item[1], reverse=True)
        top_word, top_count = sorted_counts[0]
        percentage = (top_count / total_words) * 100
        print(f"The word appearing most frequently after '{textinput}' is '{top_word}' at {percentage:.2f}%")
        currenttext.append(textinput)
        currenttext.append(top_word)
        run()
    else:
        print(f"The word '{textinput}' was not found in the text.")

def run():
    for _ in range(des):
        print("running")
        last_word = currenttext[-1] # Get the last word added
        words_after_like = []
        for i in range(len(words) - 1):
            if words[i] == last_word: # Only consider the last word added
                words_after_like.append(words[i + 1])

        word_counts = Counter(words_after_like)
        total_words = len(words_after_like)

        if total_words > 0:
            sorted_counts = sorted(word_counts.items(), key=lambda item: item[1], reverse=True)
            top_word, top_count = sorted_counts[0]
            percentage = (top_count / total_words) * 100
            print(f"The word appearing most frequently after '{last_word}' is '{top_word}' at {percentage:.2f}%")
            currenttext.append(top_word)
            
        else:
            print("No more words found.")
            break
    print(currenttext)
initial()
