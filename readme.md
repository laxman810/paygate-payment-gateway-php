
![](PayGate-Direct-Pay-Online.png)



## add card
## delete card
## charge card

For Windows

1: Find extension=php_soap.dll in php.ini and remove the semicolon(;)

2: Restart your Server

For Linux (Ubuntu)

**PHP7.x **

sudo apt-get install php7.0-soap 
sudo systemctl restart apache2

For nginx

sudo apt-get install php7.0-soap
sudo systemctl restart nginx


For PHP5

apt-get install php-soap