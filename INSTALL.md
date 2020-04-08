# Installation

You will need an installed and working playSMS, and assumed your playSMS is installed with this setup:

- Your playSMS web files is in `/var/www/html/playsms`

Follow below steps:

1. Clone this repo to your playSMS server

   ```
   cd ~
   git clone https://github.com/easysmsgr/plugin-easysms-playsms.git
   cd plugin-easysms-playsms
   ```

2. Copy gateway to playSMS `plugin/gateway/`

   ```
   cp -rR web/plugin/gateway/easysms /var/www/html/playsms/plugin/gateway/
   ```
