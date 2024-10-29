https://console.cloud.google.com/apis/credentials?project=photo-test-422819


https://artisansweb.net/integrate-google-drive-api-with-php/
https://artisansweb.net/how-to-integrate-google-sheets-api-with-php/


####Installation process

UBUNTU 20.04

Update soft, install Git, Screen and Midnight Commander
```
apt-get -y update && apt-get -y upgrade && apt-get install -y git mc screen
```

# SWAP 10Gb
```
fallocate -l 10G /swapfile
ls -lh /swapfile
chmod 600 /swapfile
mkswap /swapfile
swapon /swapfile
echo "/swapfile   none    swap    sw    0   0" >> /etc/fstab
sysctl vm.swappiness=10
sysctl vm.vfs_cache_pressure=50
echo "vm.swappiness = 10" >> /etc/sysctl.conf
echo "vm.vfs_cache_pressure = 50" >> /etc/sysctl.conf
```

# Install Hestia Control Panel
```
wget https://raw.githubusercontent.com/hestiacp/hestiacp/release/install/hst-install.sh
bash hst-install.sh -a yes -w yes -o yes -v yes -j no -k yes -m yes -g no -x yes -z yes -c yes -t yes -i yes -b yes -q no -d yes -f -y yes -e vladimirdrobnitsa@yandex.ru -p Rg7XfDEqqOVR7W6TKLBW -s www.vladimirdrobnitsa.ru
```

# Install composer
```
cd /tmp
curl -sS https://getcomposer.org/installer -o composer-setup.php
php composer-setup.php --install-dir=/usr/local/bin --filename=composer
```

# Install p-seed
```
cd /home/seed/
mkdir p-seed
chown seed:seed ./p-seed
sudo -u seed git clone git@bitbucket.org:sitd777/p-seed.git ./p-seed
cd ./p-seed
sudo -u seed composer update
```