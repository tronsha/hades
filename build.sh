#!/bin/sh
rm ./fonts/*
cp ./vendor/googlefonts/apache/calligraffitti/*.ttf ./fonts/
cp ./vendor/googlefonts/ufl/ubuntumono/*.ttf ./fonts/
cp ./vendor/font-awesome-4.6.*/fonts/* ./fonts/
rm ./css/images/*
cp ./vendor/jquery-ui-1.11.*/images/* ./css/images/
echo "" > ./tmp.css
cat ./vendor/normalize.css-4.1.*/normalize.css >> ./tmp.css
cat ./vendor/font-awesome-4.6.*/css/font-awesome.css >> ./tmp.css
cat ./vendor/jquery-ui-1.11.*/jquery-ui.css >> ./tmp.css
cat ./src/css/style.css >> ./tmp.css
cat ./src/css/theme.css >> ./tmp.css
java -jar ./vendor/yuicompressor-2.4.8.jar --type css ./tmp.css -o ./css/style.min.css
rm ./tmp.css
echo "" > ./tmp.js
cat ./vendor/jquery-1.12.*/dist/jquery.js >> ./tmp.js
cat ./vendor/jquery-ui-1.11.*/jquery-ui.js >> ./tmp.js
cat ./src/js/chat.js >> ./tmp.js
java -jar ./vendor/yuicompressor-2.4.8.jar --type js ./tmp.js -o ./js/script.min.js
rm ./tmp.js
