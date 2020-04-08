# Installation

You will need an installed and working playSMS, and assumed your playSMS is installed with these setups:

- Your playSMS web files is in `/var/www/html/playsms`
- Your playSMS database is `playsms`
- Your playSMS database username is `root`
- You access the database, you know the password

Follow below steps in order:

1. Clone this repo to your playSMS server

   ```
   cd ~
   git clone https://github.com/playsms/plugin-easysms-playsms.git
   cd plugin-easysms-playsms
   ```

2. Copy gateway to playSMS `plugin/gateway/`

   ```
   cp -rR web/plugin/gateway/easysms /var/www/html/playsms/plugin/gateway/
   ```
