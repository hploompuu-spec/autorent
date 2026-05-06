
# Autorendi veebirakendus

Dockeriga konteineriseeritud PHP-põhine autorendi infosüsteem.

## Kiirjuhend

### Docker Compose'i kasutamine (soovitatav)

Konteinerite käivitamiseks sisesta käsureale:

```bash
docker compose up -d
```

Juurdepääs rakendustele:
- **Veebirakendus:** http://localhost/
- **phpMyAdmin:** http://localhost:8080/

---

## Autentimisandmed

**phpMyAdmin:**
- **Kasutajanimi:** `root`
- **Parool:** `docker123`

**Andmebaasi kasutaja:**
- **Kasutajanimi:** `hannes`
- **Parool:** `Passw0rd`

---

## Teenused ja Funktsioonid

### Süsteemi komponendid:
* **PHP/Apache**: Veebiserver, mis töötab pordis 80.
* **MariaDB**: Relatsiooniline andmebaas pordis 3306.
* **phpMyAdmin**: Veebipõhine liides andmebaasi haldamiseks.

### Rakenduse võimalused:
* Autode nimekiri koos sorteerimise ja lehekülgedeks jaotamisega.
* Kasutajate registreerimine ja turvaline sisselogimine.
* Autorendi broneerimissüsteem.
* Administraatori töölaud andmete haldamiseks.
* Kaasaegne ja kohanduv Bootstrap kasutajaliides.

---

## Projekti struktuur

```text
car_rent/
├── Dockerfile           # PHP/Apache konteineri definitsioon
├── docker-compose.yml   # Mitme konteineri orkestreerimine
├── .dockerignore        # Dockeri ehitusest välistatud failid
├── config.php           # Andmebaasi konfiguratsioon
├── index.php            # Avaleht koos autode nimekirjaga
├── register.php         # Kasutaja registreerimine
├── admin/               # Administraatori paneel
├── db/                  # Andmebaasi tõmmised
└── header.php           # Ühine päise mall
```

---

## Arendus ja seiskamine

**Arendustöö:**
Muuda PHP-faile otse VS Code-is — tänu *bind mount* ühendusele sünroonitakse muudatused automaatselt konteinerisse.

**Konteinerite peatamine:**
```bash
docker compose down
```

**Andmete eemaldamine (andmebaasi algseadistamine):**
```bash
docker compose down -v
```

---

## Litsents
Õppeprojekt.
```
