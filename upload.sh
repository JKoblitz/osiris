python .data/profile_pictures.py
scp -r .\php osiris:/var/www/html/
scp -r .\pages osiris:/var/www/html/
scp -r .\components osiris:/var/www/html/
scp -r .\js osiris:/var/www/html/
scp -r .\img osiris:/var/www/html/
scp -r .\addons osiris:/var/www/html/
scp -r .\css osiris:/var/www/html/
scp -r .\*.php osiris:/var/www/html/
scp -r .\*.md osiris:/var/www/html/
# scp -r .\*.json osiris:/var/www/html/


scp -r .\php ambrosia:/var/www/html/
scp -r .\pages ambrosia:/var/www/html/
scp -r .\components ambrosia:/var/www/html/
scp -r .\js ambrosia:/var/www/html/
scp -r .\img ambrosia:/var/www/html/
scp -r .\css ambrosia:/var/www/html/
scp -r .\*.php ambrosia:/var/www/html/
scp -r .\*.md ambrosia:/var/www/html/
# scp -r .\*.json ambrosia:/var/www/html/
