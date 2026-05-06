# 🏎️ Autorendi veebirakendus

PHP-põhine autorendi infosüsteem, mis on mugavalt pakendatud **Docker** konteineritesse.

---

## 🛠️ Teenused ja ⚙️ Funktsioonid

### Süsteemi komponendid:
*   **🐘 PHP / 🏛️ Apache**: Veebiserver, mis töötab pordis 80.
*   **💾 MariaDB**: Relatsiooniline andmebaas pordis 3306.
*   **🔧 phpMyAdmin**: Veebipõhine liides andmebaasi haldamiseks poordis 8080.

### Rakenduse võimalused:
*   **🚗 Autode nimekiri**: Koos sorteerimise ja lehekülgedeks jaotamisega.
*   **🔑 Kasutajahaldus**: Registreerimine ja turvaline sisselogimine.
*   **📅 Broneerimine**: Autorendi broneerimissüsteem.
*   **📊 Admin paneel**: Administraatori töölaud sõidukite, kasutajate ja broneeringute haldamiseks.
*   **📱 Disain**: Kaasaegne ja kohanduv Bootstrap kasutajaliides.

---

## 🚀 Kiirjuhend

### 🐳 Docker Compose'i kasutamine (Soovitatav)

Konteinerite käivitamiseks sisesta käsureale:

```bash
docker compose up -d
```

### 🔗 Juurdepääs linkidele:
*   **🌐 Veebirakendus:** [http://localhost/](http://localhost/)
*   **🗄️ phpMyAdmin:** [http://localhost:8080/](http://localhost:8080/)

---

## 🔐 Autentimisandmed

### 🛠️ phpMyAdmin:
*   **Kasutajanimi:** `root`
*   **Parool:** `docker123`

### 👤 Andmebaasi kasutaja:
*   **Kasutajanimi:** `hannes`
*   **Parool:** `Passw0rd`

### 👤 Veebilehe kasutaja:
*   **Kasutajanimi:** `kasutaja@kasutaja.ee`
*   **Parool:** `Passw0rd`

### 👤 Veebilehe administraator:
*   **Kasutajanimi:** `admin@admin.ee`
*   **Parool:** `Passw0rd`

---

## 📂 Projekti struktuur

```text
car_rent/
├── 🐳 Dockerfile               # PHP/Apache konteineri definitsioon
├── 🐙 docker-compose.yml       # Mitme konteineri orkestreerimine
├── 📄 .dockerignore            # Dockeri ehitusest välistatud failid
├── 📄 .gitignore               # Git-i versioonihaldusest välistatud failid
├── ⚙️ config.php               # Andmebaasi konfiguratsioon
├── 🏠 index.php                # Avaleht koos autode nimekirjaga
├── 📝 register.php             # Kasutaja registreerimine
├── 🧩 header.php               # Ühine päise mall
│
└── 🛡️ admin/                   # Administraatori paneel
    ├── 📊 index.php            # Admini töölaud
    ├── 🔑 login.php            # Admini sisselogimine
    ├── 🚪 logout.php           # Väljalogimise haldur
    ├── 🛡️ admin_check.php      # Autentimise kontroll
    ├── ➕ lisa.php             # Uue auto lisamine
    ├── ✏️ muuda.php            # Auto andmete muutmine
    ├── 🗑️ kustuta.php           # Auto kustutamine
    ├── 👥 users.php            # Kasutajate haldamine
    ├── 📜 my_rentals.php       # Rendiajaloo vaade
    ├── 📅 muuda_broneering.php  # Broneeringu muutmine
    └── ❌ kustuta_broneering.php # Broneeringu tühistamine
```

---

## 💻 Arendus ja 🛑 Seiskamine

### 🛠️ Arendustöö:
Muuda PHP-faile otse **VS Code**-is — tänu *bind mount* ühendusele sünroonitakse muudatused automaatselt konteinerisse.

### 🔌 Konteinerite peatamine:
Konteinerite töö lõpetamiseks kasuta käsku:
```bash
docker compose down
```

### 🧹 Andmete täielik eemaldamine:
*(Tähelepanu: See eemaldab ka andmebaasi sisu ja mahud)*
```bash
docker compose down -v
```
