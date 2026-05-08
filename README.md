# Autorendi veebirakendus

PHP-põhine autorendi infosüsteem, mis on pakendatud Docker Compose'i abil käivitatavaks veebirakenduseks. Rakendus võimaldab kasutajatel sirvida autosid, teha broneeringuid ning hallata oma rendiajalugu. Administraatoril on eraldi töölaud autode, kasutajate ja broneeringute haldamiseks.

---

## Teenused ja süsteemi komponendid

Rakendus koosneb kolmest Docker Compose'i teenusest:

* **PHP / Apache** - veebirakendus, mis töötab pordis `80`.
* **MariaDB** - relatsiooniline andmebaas, kuhu salvestatakse autod, kasutajad ja broneeringud.
* **phpMyAdmin** - veebipõhine andmebaasi haldusliides pordis `8080`.

Andmebaas luuakse käivitamisel automaatselt `db` kaustas olevate SQL-failide põhjal. Rakenduse failid on konteinerisse ühendatud bind mount'iga, seega PHP-failide muutmine jõuab arenduse ajal kohe veebiserverisse.

---

## Rakenduse võimalused

### Avalik autokataloog

Esilehel kuvatakse autorendi valikus olevad sõidukid kaartidena. Iga auto juures on nähtav mark, mudel, mootor, kütus ja päevahind. Nimekiri on jaotatud lehekülgedeks, et ka suurema autopargi puhul oleks sirvimine mugav.

Autode nimekirja saab sorteerida:

* margi järgi A-Z või Z-A;
* mudeli järgi A-Z või Z-A;
* hinna järgi kasvavalt või kahanevalt;
* väljalaskeaasta järgi uuemast vanemani või vanemast uuemani.

### Auto detailvaade ja rentimine

Auto detailvaates kuvatakse põhjalikum info valitud sõiduki kohta: mark, mudel, mootor, kütus, aasta, staatus, käigukast, istekohtade arv ja päevahind. Lisaks näeb kasutaja sama auto olemasolevaid broneeritud perioode, et vältida kattuvaid rendiaegu.

Sisselogitud kasutaja saab auto rentimiseks valida algus- ja lõppkuupäeva ning märkida, kas soovib lisakindlustust. Rakendus kontrollib broneeringu tegemisel, et:

* kuupäevad oleksid korrektses vormingus;
* tavakasutaja ei saaks valida minevikus algavat broneeringut;
* lõppkuupäev ei oleks alguskuupäevast varasem;
* valitud periood ei kattuks sama auto olemasoleva broneeringuga.

Kui broneering õnnestub, arvutatakse koguhind auto päevahinna ja rendipäevade arvu põhjal ning broneering salvestatakse staatusega `broneeritud`.

### Kasutajakonto ja autentimine

Kasutaja saab end registreerida eesnime, perekonnanime, e-posti, telefoni ja parooliga. Registreerimisel kontrollitakse kohustuslikke välju, e-posti formaati, parooli minimaalset pikkust ja parooli kinnitust. Parool salvestatakse andmebaasi räsituna.

Sisselogimine toimub e-posti ja parooliga. Eduka sisselogimise järel suunatakse tavakasutaja avalehele ning administraator admini töölauale. Rakendus kasutab sessioone, CSRF-token'eid ja turvapäiseid, et vähendada tavapäraseid veebirakenduse riske.

### Kasutaja broneeringud

Sisselogitud kasutaja saab vaadata oma broneeringuid eraldi vaates. Broneeringute tabelis on näha auto, alguskuupäev, lõppkuupäev, koguhind, lisakindlustuse valik ja staatus.

Broneeringuid saab filtreerida auto, staatuse ning algus- ja lõppkuupäeva järgi. Kasutaja saab oma broneeringut muuta või kustutada. Muutmisel arvutatakse koguhind kuupäevade põhjal uuesti.

### Administraatori töölaud

Administraatori vaade on mõeldud autorendi töötajale või süsteemi haldajale. Administraator saab hallata kogu autoparki tabelivaates, kus on näha auto põhiandmed, pilt, kirjeldus, staatus ja hind.

Administraatori autode tabel toetab:

* otsingut margi järgi;
* sorteerimist ID, margi, mudeli, mootori, kütuse, aasta, käigukasti, istekohtade, staatuse ja hinna alusel;
* auto lisamist;
* auto andmete muutmist;
* auto kustutamist.

Auto lisamisel ja muutmisel saab sisestada margi, mudeli, mootori, kütuse, hinna, aasta, käigukasti, istekohtade arvu, kirjelduse ja staatuse. Lisaks saab üles laadida auto pildi. Lubatud on JPG, JPEG, PNG, GIF ja WebP failid kuni 2 MB.

### Kasutajate haldamine

Administraator saab vaadata kõiki registreeritud kasutajaid, muuta nende eesnime, perekonnanime, e-posti ja rolli ning kustutada kasutajaid. Rakendus ei luba administraatoril omaenda kontot kasutajate halduse kaudu kustutada.

Rollide abil eristatakse tavakasutajat ja administraatorit. Administraatori õigused on vajalikud admini töölaua, autode halduse ja kõigi broneeringute haldamise jaoks.

### Broneeringute haldamine

Administraator näeb kõiki süsteemis olevaid broneeringuid, mitte ainult enda omi. Broneeringute vaates saab filtreerida tulemusi auto, staatuse, alguskuupäeva, lõppkuupäeva ja kasutaja järgi.

Administraator saab broneeringuid muuta, kustutada ning muuta broneeringu staatust. Tavakasutaja saab muuta enda broneeringu kuupäevi ja lisakindlustuse valikut, kuid staatuse muutmine on reserveeritud administraatorile.

---

## Kiirjuhend

### Docker Compose'i kasutamine

Konteinerite käivitamiseks sisesta käsureale:

```bash
docker compose up -d
```

### Juurdepääs linkidele

* **Veebirakendus:** [http://localhost/](http://localhost/)
* **phpMyAdmin:** [http://localhost:8080/](http://localhost:8080/)

---

## Autentimisandmed

### phpMyAdmin

* **Kasutajanimi:** `root`
* **Parool:** `docker123`

### Andmebaasi kasutaja

* **Kasutajanimi:** `hannes`
* **Parool:** `Passw0rd`

### Veebilehe kasutaja

* **Kasutajanimi:** `kasutaja@kasutaja.ee`
* **Parool:** `Passw0rd`

### Veebilehe administraator

* **Kasutajanimi:** `admin@admin.ee`
* **Parool:** `Passw0rd`

---

## Projekti struktuur

```text
car_rent/
|-- Dockerfile                 # PHP/Apache konteineri definitsioon
|-- docker-compose.yml         # PHP, MariaDB ja phpMyAdmin teenused
|-- .dockerignore              # Dockeri ehitusest välistatud failid
|-- .gitignore                 # Git-i versioonihaldusest välistatud failid
|-- config.php                 # Andmebaasi ühendus ja seadistus
|-- security.php               # Sessioonid, CSRF, turvapäised ja abifunktsioonid
|-- header.php                 # Ühine päise ja navigatsiooni mall
|-- index.php                  # Avalik autode nimekiri
|-- single_car.php             # Auto detailvaade ja broneerimine
|-- register.php               # Kasutaja registreerimine
|-- db/
|   |-- db.sql                 # Andmebaasi struktuur ja algandmed
|   `-- car_rent.sql           # Andmebaasi SQL-andmed
`-- admin/
    |-- index.php              # Administraatori autode töölaud
    |-- login.php              # Sisselogimine
    |-- logout.php             # Väljalogimine
    |-- admin_check.php        # Administraatori õiguste kontroll
    |-- lisa.php               # Uue auto lisamine
    |-- muuda.php              # Auto andmete muutmine
    |-- kustuta.php            # Auto kustutamine
    |-- users.php              # Kasutajate haldamine
    |-- my_rentals.php         # Kasutaja või admini broneeringute vaade
    |-- muuda_broneering.php   # Broneeringu muutmine
    `-- kustuta_broneering.php # Broneeringu kustutamine
```

---

## Arendus ja seiskamine

### Arendustöö

Muuda PHP-faile otse projektikaustas. Docker Compose kasutab bind mount'i, mistõttu muudatused sünkroonitakse automaatselt konteinerisse ja on brauseris kohe nähtavad.

### Konteinerite peatamine

Konteinerite töö lõpetamiseks kasuta käsku:

```bash
docker compose down
```

### Andmete täielik eemaldamine

Tähelepanu: see eemaldab ka andmebaasi mahu ja selles olevad andmed.

```bash
docker compose down -v
```
