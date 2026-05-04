
# 🚗 Autorendi veebirakendus

See on koolitööks olnud ja GitHubist kloonitud PHP-põhine autorendi infosüsteem, mis on seadistatud jooksma Debian Linux serveril.

## 🛠️ Süsteemi nõuded ja paigaldus

Projekti jooksutamiseks paigaldatakse  Debian järgmised komponendid:
* **Veebiserver:** Apache2
* **Andmebaas:** MariaDB / MySQL
* **Keel:** PHP (koos mysqli laiendusega)
* **Haldusliides:** phpMyAdmin

### 1. Tarkvara paigaldamine
Serverile paigaldatud vajalik tarkvara järgmiste käskudega:
```bash
sudo apt update
sudo apt install -y apache2 mariadb-server php libapache2-mod-php php-mysql phpmyadmin git 
```
### 2. Andmebaasi ja kasutaja seadistamine
Enne kui kloonin GitHubist andmebaasi, annan järgmiste käskudega andmebaasile kasutaja ning sellele vajalikud õigused:
 ```SQL
CREATE DATABASE car_rent; 
CREATE USER 'hannes'@'localhost' IDENTIFIED BY 'Passw0rd';
GRANT ALL PRIVILEGES ON car_rent.* TO 'hannes'@'localhost'; FLUSH PRIVILEGES;
EXIT;
```

### 3. Projekti kloonimine GitHubist

Kuna GitHub nõuab parooli asemel **Tokenit**, loon GitHubis vajaliku tokeni ning kasutn järgmist meetodit:

1.  Liigu veebikausta: `cd /var/www/html`
    
2.  Eemalda vana index.html faili: `sudo rm index.html`
    
3.  Klooni projekt (asenda `TOKEN` oma GitHubi Personal Access Tokeniga):

```bash
sudo git clone https://hploompuu-spec:TOKEN@github.com/hploompuu-spec/car_rent.git  
```
NB!  Kuna soovin, et failid asuksid juurkaustas `/var/www/html` siis kasutan järgmisi käske, et kopeerida ja kustutada kaust `car_rent`
```bash
cd /var/www/html
sudo mv car_rent/* . 
sudo mv car_rent/.* . 2>/dev/null 
sudo rmdir car_rent
```

4.  Annan veebiserverile õigused faile lugeda ja kirjutada:

```bash
sudo chown -R www-data:www-data /var/www/html 
sudo chmod -R 755 /var/www/html
```

### 4. Andmebaasi seadistamine phpmyadmini kaudu

Nüüd avan brauseris `http://(masina ip aadress)/phpmyadmin` ja impordin DB kaustas oleva kõige ajakohasema andmebaasi faili `.sql` fail  vastloodud `car_rent` tabelisse.

### 5. Kontrollimine, kas andmebaas käivitub

Kontrollides brauseris `http://(masina ip aadress)` saab veenduda, et andmebaas töötab.

![veebileht](https://github.com/user-attachments/assets/82cc689d-1f67-47bd-ac3f-7cc6c0d9b691)

Aadressil `http://(masina ip aadress)/admin` käivitub ka administreerimisleht.

<img width="1428" height="827" alt="admin" src="https://github.com/user-attachments/assets/a4ed81c0-4660-4fc8-9b9c-059b345f28f2" />

### 6. Tabelite lisamine
Lisan tabelid vastavalt juhendis antud andmetele.
Kasutajate tabel `users`:

<img width="997" height="242" alt="users2" src="https://github.com/user-attachments/assets/81544373-51ed-4b84-bd03-7509aae0b1b3" />

Broneeringute tabel `reservations`:

<img width="1020" height="248" alt="reservations" src="https://github.com/user-attachments/assets/9c15aaed-16e3-4e44-bb70-1009f4a21adb" />


### 7. Tabelitesse andmete lisamine
Andmete lisamiseks genereerin veebilehte Mockaroo kasutades vajalikud tabelid nii `users` kui `reservations` tabelite jaoks. Saadud failid kättesaadavad kaustas `DB`. 
Kausta `users` sisu näeb välja selline:

<img width="1381" height="579" alt="users_tabel" src="https://github.com/user-attachments/assets/3686e015-e7ed-4fb8-87c2-425b71365bc3" />

### 8. Andmebaasi koopia ja üleslaadimine GitHubi
Andmebaasi koopia tegemiseks kaustas `/var/wwww/html/` kasutan käsku:
```bash
mysqldump -u hannes -p car_rent > andmebaas.sql
```
(selguse mõttes lisatud andmebaasile juurde kuupäev formaadis ppkkaa).

Kõikide muudatuste lisamine käsuga:
```bash
git add .
```
Enne andmete saatmist saab juurde lisada kommentaari ning failid üles laadida:
```bash
git commit -m "Muudetud andmebaasi koopia"
git push origin main
```

