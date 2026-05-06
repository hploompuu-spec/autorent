🏎️ Autorendi Veebirakendus

PHP-põhine autorendi infosüsteem, mis on mugavalt pakendatud **Docker** konteineritesse.

---

## 🚀 Kiirjuhend

### 🐳 Docker Compose'i kasutamine (Soovitatav)

Konteinerite käivitamiseks sisesta käsureale:

```bash
docker compose up -d
🔗 Juurdepääs linkidele:
🌐 Veebirakendus: http://localhost/

🗄️ phpMyAdmin: http://localhost:8080/

🔐 Autentimisandmed
🛠️ phpMyAdmin:
Kasutajanimi: root

Parool: docker123

👤 Andmebaasi kasutaja:
Kasutajanimi: hannes

Parool: Passw0rd

🛠️ Teenused ja ⚙️ Funktsioonid
Süsteemi komponendid:
🐘 PHP / 🏛️ Apache: Veebiserver, mis töötab pordis 80.

💾 MariaDB: Relatsiooniline andmebaas pordis 3306.

🔧 phpMyAdmin: Veebipõhine liides andmebaasi haldamiseks.

Rakenduse võimalused:
🚗 Autode nimekiri: Koos sorteerimise ja lehekülgedeks jaotamisega.

🔑 Kasutajahaldus: Registreerimine ja turvaline sisselogimine.

📅 Broneerimine: Autorendi broneerimissüsteem.

📊 Admin paneel: Administraatori töölaud andmete haldamiseks.

📱 Disain: Kaasaegne ja kohanduv Bootstrap kasutajaliides.

📂 Projekti struktuur
Plaintext
car_rent/
├── 🐳 Dockerfile           # PHP/Apache konteineri definitsioon
├── 🐙 docker-compose.yml   # Mitme konteineri orkestreerimine
├── 📄 .dockerignore        # Dockeri ehitusest välistatud failid
├── ⚙️ config.php           # Andmebaasi konfiguratsioon
├── 🏠 index.php            # Avaleht koos autode nimekirjaga
├── 📝 register.php         # Kasutaja registreerimine
├── 🛡️ admin/               # Administraatori paneel
├── 💾 db/                  # Andmebaasi tõmmised
└── 🧩 header.php           # Ühine päise mall
💻 Arendus ja 🛑 Seiskamine
🛠️ Arendustöö:
Muuda PHP-faile otse VS Code-is — tänu bind mount ühendusele sünroonitakse muudatused automaatselt konteinerisse.

🔌 Konteinerite peatamine:
Bash
docker compose down
🧹 Andmete täielik eemaldamine:
(Eemaldab ka andmebaasi sisu)

Bash
docker compose down -v
📜 Litsents
🎓 Õppeprojekt.
