function sampleFunction {
console.log(("str1,str2,str3,str4".match(new RegExp("str", "g")) || []).length);
}