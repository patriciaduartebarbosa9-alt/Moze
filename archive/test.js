const path = require('path');
let testPath = "templates";  // Simulate your issue
console.log(path.join(__dirname, testPath));  // This will throw the error
console.log("ola");  // This will throw the error
