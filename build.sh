#!/bin/sh
echo "" > ./tmp.css
cat ./css/normalize.css >> ./tmp.css
cat ./css/font-awesome.css >> ./tmp.css
cat ./css/jquery-ui.css >> ./tmp.css
cat ./css/style.css >> ./tmp.css
cat ./css/theme.css >> ./tmp.css
java -jar ./yuicompressor-2.4.8.jar --type css ./tmp.css -o ./css/style.min.css
rm ./tmp.css
echo "" > ./tmp.js
cat ./js/jquery.js >> ./tmp.js
cat ./js/jquery-ui.js >> ./tmp.js
cat ./js/chat.js >> ./tmp.js
java -jar ./yuicompressor-2.4.8.jar --type js ./tmp.js -o ./js/script.min.js
rm ./tmp.js
