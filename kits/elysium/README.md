# Installation

AEON_KIT_NAME theme uses [Gulp](http://gulpjs.com) to compile Sass and Javascript. Gulp needs Node.

#### Step 1
Make sure you have Node and npm installed.
You can read a guide on how to install node here: https://docs.npmjs.com/getting-started/installing-node

#### Step 2
Install bower: `npm install -g bower`.

#### Step 3
Go to the `dev` folder of AEON_KIT_NAME theme and run the following commands: `npm run setup`.

#### Step 4
Update `browserSync.proxy` in **dev/config/config.local.json**.

#### Step 5
Run the following command to compile Sass and watch for changes: `gulp`.
