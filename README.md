# RandA PhotoSharing


## Hardware
RandA PhotoSharing is an application which will require the following components:
- RaspBerry Pi 2
- RaspBerry Camera
- SD Card (8GB or more)
- RandA
- HDMI monitor/TV
- USB mouse and keyboard
- 3 buttons and 2 conducting poles
- Wired or wireless Internet connection (optional)

It’ll also need:
- A GMail account to send emails and save photos to cloud
- An IFTTT account to post photos on various social networks (optional)
- Social networks accounts on which the photos will be posted (optional)

Assembly instructions:

1. Connect the RaspBerry Camera to RaspBerry
2. Connect RandA to RaspBerry
3. Connect mouse, keyboard ethernet cable or wireless stick which will be needed for the configuration part
4. Connect the 3 buttons to RandA as follows:
4. Button “Next” → pin 8
5. Button “OK” → pin 9
6. Button “Prev” → pin 10
7. Power the buttons by connecting them to the 5V and GND pins on RandA
8. Connect the two metal poles to the 5V and A0 pins on RandA
9. Setup your SD card (we’ll explain it in the next section) and insert it in your RaspBerry, power RandA with a 5V/2A USB cable, this will also power RaspBerry

## Setup
You can download a preconfigured ISO image from Elettronica In’s website and skip to the sketch upload section, otherwise you can download the latest Raspbian image from RaspBerry’s website and follow the step-by-step setup.
Burn the RaspBian image on the SD card using Win32DiskImager which can be downloaded here: (sourceforge.net/projects/win32diskimager/)
Insert the SD card in your RaspBerry and power up RandA.

During the first boot you’ll be prompted with the RaspBerry configuration screen, on which you’ll have to accomplish the following:

1. Expand Filesystem
2. Enable Boot to Desktop/Scratch: select “Desktop Log in…”
3. Internalisation Options:
  1. Change Locale: deselect *en_GB.UTF-8 UTF-8 using SPACEBA*R and select *it_IT ISO-8859-1* instead(setting it as “Default locale for the system environment”)
  2. Change Timezone: select Europe and then Rome
  3. Change Keyboard Layout: select the keyboard model you’re using(usually *Generic 105 keys (Intl) PC*), and then select     Other and *Italian* as keyboard language, choose *Italian* as keyboard layout, finally confirm with *Ok* twice
4. Enable Camera: *Enable*
5. Advanced Options:
  1. SSH: Enable
  2. I2C: answer *Yes* to both questions
Move your cursor by pressing TAB to select *<Finish>* then confirm with ENTER to reboot.

The following configuration steps can be executed either via RaspBerry’s GUI or via SSH using a client like Putty.

An internet connection will be necessary to proceed: if you’re using an ethernet cable skip this section, otherwise plug your USB WiFi dongle in and follow these steps:
Open your terminal and run dmesg | more
Make sure that RaspBerry recognized the peripheral: press SPACE until you’ll find a value like “Product: 802.11n WLAN Adapter ...” and then press Q to quit
Edit the network interfaces file typing: sudo nano /etc/network/interfaces
Insert the following lines to enable a WiFi connection:
auto lo
iface lo inet loopback
iface eth0 inet dhcp

allow-hotplug wlan0
auto wlan0

iface wlan0 inet dhcp
  wpa-ssid "Your WiFi network name"
  wpa-psk "Your WiFi network password"
Run the following command to reload the networking settings: sudo service networking reload
Using ifconfig you can check if the WiFi dongle is actually connected to the network and has been assigned an IP address.

Now that your RaspBerry has acquired internet access, run the following command to update the system and reboot:
sudo apt-get update && sudo apt-get upgrade && sudo rpi-update && sudo reboot
This will require some time and you’ll have to confirm some packets’ setup by pressing Y and then ENTER.

Download the latest RandA setup from https://github.com/open-electronics/RandA/releases
In the archive you’ll find a readme that explains how to correctly setup RandA (InstallationREADME.TXT) and the setup files.

After having installed RandA and tested the serial communication with RaspBerry, you’ll have to change Tomcat’s listening port to avoid conflicts with Apache which we’ll install later.
Tomcat is used to install RandA’s control panel which is obsolete for our application.
On the other hand we’ll use Apache to host a simple control panel for RandA PhotoSharing.
Change directory to the one containing Tomcat’s configuration files:
cd /home/apache-tomcat-7.0.47/conf
Open the file server.xml: nano server.xml  and edit the row “<Connector port = “80” …”  replacing 80 with 8080.
Reboot RaspBerry: sudo reboot

Force RaspBerry to never disable its HDMI output by editing the following file:
sudo nano /etc/lightdm/lightdm.conf
Add the following row to the section [SeatDefaults]:
xserver-command=X -s 0 dpms
Close and save with CTRL+X, Y and ENTER.

Run the following commands in order to install Apache, PHP and the MySQL server we’ll need to power the RandA PhotoSharing control panel.
sudo apt-get install apache2 apache2-doc apache2-utils
sudo apt-get install libapache2-mod-php5 php5 php-pear php5-xcache
sudo apt-get install php5-mysql
sudo apt-get install mysql-server mysql-client
During your setup some packets will require you to confirm their installation, press Y and then ENTER to confirm.
When you’ll be prompted for a database password, type it in and remember it for later;
Install PHPMyAdmin to manage the database:
sudo apt-get install phpmyadmin
You’ll be asked which web server you wish to automatically configure, select apache2 using SPACEBAR, then confirm by pressing TAB and then ENTER.
Accept to configure PHPMyAdmin’s database using dbconfig-common by answering “Yes” when you’re asked.
When the setup is done, open apache2 config file:
sudo nano /etc/apache2/apache2.conf
And insert the following instruction at the bottom of the file:
Include /etc/phpmyadmin/apache.conf
Close and save by pressing CTRL+X, Y and ENTER.
Restart apache2 to load the latest changes:
sudo /etc/init.d/apache2 restart

Now you can install all the libraries necessary to the Python script by running the following commands in order:
sudo apt-get install python-dev
sudo apt-get install python-imaging-tk
sudo apt-get install python-mysqldb
sudo apt-get install imagemagick

Install the mail server and other utilities you’ll need to sebd eMails using Python by running the following commands in order:
sudo apt-get install ssmtp
sudo apt-get install mailutils

Edit the ssmtp.conf file:
sudo nano /etc/ssmtp/ssmtp.conf
So that it’ll look like this:
root=postmaster
mailhub=smtp.gmail.com:587
hostname=raspberrypi
AuthUser=GmailAccount@gmail.com
AuthPass=GmaiPasswordl
UseSTARTTLS=YES
Swap “GmailAccount” and “GmailPassword” with your email address and password.
Save and quit by pressing CTRL+X, Y and ENTER.

Edit the revaliases file:
sudo nano /etc/ssmtp/revaliases
Add the following row:
root:root@gmail:smtp.gmail.com:587
Save and quit by pressing CTRL+X, Y and ENTER.
Change permissions over the ssmtp.conf file by running:
sudo chmod 774 /etc/ssmtp/ssmtp.conf

Reboot:
sudo reboot

Now your RaspBerry is fully configured, proceed by downloading RandA PhotoSharing, move to the folder /var/www:
cd /var/www
Download RandA PhotoSharing by running:
git clone https://github.com/open-electronics/randaps.git
The downloaded folder contains:
admin/: this folder contains all the web panel files
data/: this folder contains all the pictures needed for the GUI (themes, overlays, logos, …)
photos:/ this folder contains all the pictures that will be taken
randaps.py: RandA PhotoSharing python script
randaps_sketch.hex: compiled RandA source code ready to be uploaded
randaps_sketch.ino: non-compiled RandA source code
RandA-PhotoSharing.sql: database installation
README.pdf: complete guide about RandA PhotoSharing setup and utilization

Type in your web browser url-bar “http://IP_RASPBERRY/phpmyadmin” swapping “IP_RASPBERRY” with its effective IP address.
Login using root as your username and the password you chose during MySQL and PHPMyAdmin setup.
Click on the tab Privileges, click Add a new user and fill in the fields as follows:
Username: [text field] randaps
Host: [Local] localhost
Password: [text field] randaps
Re-type: randaps
Database for user: Create a database with same name and grant all privileges
Global privileges: Select all
Click on Create user.
On the right side of the screen you’ll see the new database randaps (otherwise refresh the pace), click on it and then on the Import tab.
You have to import the SQL file located into the RandA PhotoSharing folder: select the file and click Execute.

Edit the RandA-PhotoSharing script:
sudo nano /var/www/randaps/randaps.py
In the “CUSTOMIZABLE VARIABLES” section instert your GMail password replacing “INSERT_YOUR_EMAIL_PASSWORD”, without removing the quotes; 
Close and save by pressing CTRL+X, Y and ENTER.

Run: sudo visudo and add the following line at the bottom of the files:
www-data ALL=(ALL) NOPASSWD: ALL
Close and save by pressing CTRL+X, Y and ENTER: this way we’ll grant PHP the permission to act on the photos.

Upload the sketch on RandA running:
ArduLoad /var/www/randaps/randaps_sketch.hex

Now you’ll have to configure all the accounts that RandA PhotoSharing will need to send the photos:
Gmail: RandA PhotoSharing will use this account to send the pictures to IFTTT and to the user (if he fills in the email field)
IFTTT: every time it’ll receive an email from a known GMail account, it’ll upload it on Google Drive (if enabled) and share it on Facebook or Twitter (if enabled)

Log into your GMail account and go to the following address: https://www.google.com/settings/security/lesssecureapps and enable access for less secure apps, this way RandA PhotoSharing will be able to send eMails on behalf of this account.

Create an IFTTT account (www.ifttt.com) and add the following recipes:
Save photos on Google Drive: https://ifttt.com/recipes/192360-send-gmail-attachments-to-google-drive
Upload photos on Twitter: https://ifttt.com/recipes/129743-twitter-subject-w-tw-body-tweet-attachment-photo
Upload photos on Facebook: https://ifttt.com/recipes/13714-upload-photo-to-facebook-from-e-mail

