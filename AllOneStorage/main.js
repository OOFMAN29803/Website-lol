 
 function noneStorageFunction() {
	 var mainIndex = document.getElementById("pricingIndex")
	 var texty = document.getElementById("textToken")
	 var pricing = document.getElementById("pricingText")
	 var titleBase = document.getElementById("topPricingTitleBase")
	 var buttonBuy = document.getElementById("buyButton")
 	 var progressBar = document.getElementById("selectionBarUsed")
	 progressBar.style.width = "1.8%"
	 pricing.style.display = "none"
	 buttonBuy.style.display = "none"
	 mainIndex.style.backgroundColor = "#D9D9D9"
	 mainIndex.style.border = "3px solid #B0B0B0"
	 mainIndex.style.width = "50%"
	 texty.textContent = "You don't have any storage. Which is sad, who wouldn't get cheap and efficient storage, your friends MUST be laughing at you! It is so cheap to get it, it's like pocket change. Tiny amounts every month, fast efficient storage, the best kind."
	 titleBase.textContent = "None"
 }
 
  function someStorageFunction() {
	 var mainIndex = document.getElementById("pricingIndex")
	 var texty = document.getElementById("textToken")
	 var pricing = document.getElementById("pricingText")
	 var titleBase = document.getElementById("topPricingTitleBase")
	 var progressBar = document.getElementById("selectionBarUsed")
	 var buttonBuy = document.getElementById("buyButton")
	 pricing.style.display = "block"
	 buttonBuy.style.display = "block"
	 progressBar.style.width = "51.8%"
	 mainIndex.style.backgroundColor = "#FFED4F"
	 mainIndex.style.border = "3px solid #D9C943"
	 mainIndex.style.width = "70%"
	 texty.textContent = "Its a clear and easy way to access your files quickly and easily, no hidden fees, no secrets. Nothing, just files, for more information check the Terms and Legal on the bottom of the home page."
	 titleBase.textContent = "Basic"
 }
 
   function fullStorageFunction() {
	 var mainIndex = document.getElementById("pricingIndex")
	 var texty = document.getElementById("textToken")
	 var pricing = document.getElementById("pricingText")
	 var titleBase = document.getElementById("topPricingTitleBase")
	 var progressBar = document.getElementById("selectionBarUsed")
	 var buttonBuy = document.getElementById("buyButton")
	 pricing.style.display = "block"
	 buttonBuy.style.display = "block"
	 console.log("done")
	 progressBar.style.width = "100%"
	 mainIndex.style.backgroundColor = "#FFE70A"
	 mainIndex.style.border = "3px solid #EBD509"
	 mainIndex.style.width = "70%"
	 texty.textContent = "Highest tier, one of the cheapest storage prices on the market. Excellent for normal people and techies alike. You would be spending at least 50c less or the same price, for double the amount, and you aren't walled in a garden. "
	 titleBase.textContent = "Premium"
	 	 buttonBuy.onclick = window.location.href = 'pricing/workplacePricing.html'

 }
 
 
 
 someStorageFunction();