---

## 📜 Litsents
🎓 **Õppeprojekt.**Siin on täielik, visuaalselt täiendatud **README.md** faili sisu, mis on valmis otse kopeerimiseks ja oma projekti juurkausta salvestamiseks.
```markdown
# 🏎️ Autorendi Veebirakendus

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
├── 📄 .dockerignore        # Dockeri ehitusest välistatud failid[cite: 1]
├── ⚙️ config.php           # Andmebaasi konfiguratsioon[cite: 1]
├── 🏠 index.php            # Avaleht koos autode nimekirjaga[cite: 1]
├── 📝 register.php         # Kasutaja registreerimine[cite: 1]
├── 🛡️ admin/               # Administraatori paneel[cite: 1]
├── 💾 db/                  # Andmebaasi tõmmised[cite: 1]
└── 🧩 header.php           # Ühine päise mall[cite: 1]
💻 Arendus ja 🛑 Seiskamine
🛠️ Arendustöö:
Muuda PHP-faile otse VS Code-is — tänu bind mount ühendusele sünroonitakse muudatused automaatselt konteinerisse[cite: 1].

🔌 Konteinerite peatamine:
Konteinerite töö lõpetamiseks kasuta käsku[cite: 1]:

Bash
docker compose down
🧹 Andmete täielik eemaldamine:
(Tähelepanu: See eemaldab ka andmebaasi sisu ja mahud)

[cite: 1]

Bash
docker compose down -v
📜 Litsents
🎓 Õppeprojekt.

[cite: 1]
