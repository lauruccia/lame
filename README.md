# LAMÈSS — pagina "in arrivo"

Pagina statica con raccolta email.

- `index.html` — la pagina
- `subscribe.php` — salva le email in `data/iscrizioni.csv` (cartella chiusa al web da `data/.htaccess`)
- `.cpanel.yml` — istruzioni di deploy per cPanel "Git Version Control"

## Collegare il repository a cPanel

1. cPanel → **Git™ Version Control** → **Create**.
2. Attiva **Clone a Repository**, incolla l'URL `https://github.com/lauruccia/lame.git`
   (repository pubblico: non servono credenziali. Se lo rendi privato, usa un URL SSH con chiave).
3. **Repository Path**: una cartella FUORI da `public_html`, per esempio `/home/UTENTE/repositories/lame`.
4. Create. Poi **Manage → Pull or Deploy → Update from Remote → Deploy HEAD Commit**.
   Il deploy copia `index.html`, `subscribe.php` e `data/.htaccess` in `public_html`.

Per aggiornare il sito: `git push` da GitHub, poi in cPanel **Update from Remote** e **Deploy HEAD Commit**.

Note
- Il deploy non tocca `data/iscrizioni.csv` già presente sul server.
- cPanel richiede che nel repository sul server non ci siano modifiche non committate, altrimenti il deploy non parte.
- Per ricevere anche una mail a ogni iscrizione, imposta `NOTIFY_TO` in `subscribe.php`.
