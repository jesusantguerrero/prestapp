<p align="center"><a href="/" target="_blank"><img src="/Art/logo.svg" width="400" alt="Laravel Logo"></a></p>

<h3 align="center">
    The Loan & Property Management Software
</h3>

 <p align="center">
	<strong>
		<a href="https://icloanapp.com" target="_blank" style="color:dodgerblue;">Website</a>
		•
		<a href="https://docs.icloanapp.com/" target="_blank">Docs</a>
		•
		<a href="https://icloanapp.com/" target="_blank">Demo</a>
	</strong>
</p>

## About ICLoan
ICLoan is a Loan & Property Management Software that can handle properties with multiple units, owner distribution, rental contracts and rental expenses along with loan with rates.


## ✨Features:

* ### 💵 Loans:
* ### 🏡 Property Management
* ### 👨‍👩‍👧 Integrated Loan + Property Management dashboard**

## Scope
ICLoan is an exploration in the property management area and it is a good alternative for property managers with an small portfolio. Compared to more mature software ICLoan doesnt handle 

- Accounting (event though we make the transactions under the hood that section is not public), 
- Maintenance
- Tasks
- Public Listing

## Technical Stuff
ICLoan is a Monolith app using laravel 9, jetstream, inertia, vue3, Tailwindcss and some hand crafted packages [Atmosphere UI](https://github.com/jesusantguerrero/atmosphere-ui), [Journal](https://github.com/insane-code/journal), and others.


| Prerequisite                                          | Version     |
| ------------------------------------------------------| ----------  |
| [Node.js](http://nodejs.org)                          | `~ ^16.18.0`|
| npm (comes with Node)                                 | `~ ^8.19.2` |
| [PHP]                                                 | `~ ^8.1.2`  |
| [Composer](https://getcomposer.org/)                  | ' ^2.3.8    |
| [MariaDB](https://mariadb.org/)***                    |  `10.8.4`   |
| [Cloud Platform Project (with Gmail API)**](https://developers.google.com/gmail/api/quickstart/js)                                |    --                                                 |             |
| PHP extension ext-mailparse**                         |      --     |

`** Those requirements are optional for Gmail integration/automation`
`*** MariaDB could be replaced with MySql8`

```shell
node -v
php -v
```

## Installation

To install Loger, you'll need to clone or download this repo:

```
git clone https://github.com/jesusantguerrero/atmosphere.git project_name
```

Next, we can install Atmosphere with these **4 simple steps**:

### 1. Create a New Database

During the installation we need to use a MySQL database. You will need to create a new database and save the credentials for the next step.

### 2. Copy the `.env.example` file

We need to specify our Environment variables for our application. You will see a file named `.env.example`, you will need to duplicate that file and rename it to `.env`.

Then, open up the `.env` file and update your *DB_DATABASE*, *DB_USERNAME*, and *DB_PASSWORD* in the appropriate fields. You will also want to update the *APP_URL* to the URL of your application.

```bash
APP_URL=http://127.0.0.1:8000/

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=prestapp
DB_USERNAME=root
DB_PASSWORD=
```

### 3. Add Composer Dependencies
```php
composer install --ignore-platform-reqs
```
### 4. Run Migrations and Seeds

```bash
php artisan migrate --seed
php artisan app:demo-fresh-start
```
<br>

backend development
```bash
php artisan serve
```
Frontend development
The backend have to be running

```bash
# install npm packages
npm install
# development
npm run dev
```

🎉 And that's it! You will now be able to visit your URL and see your Atmosphere application up and running.

## License
[BSD-3 license](https://github.com/jesusantguerrero/atmosphere/blob/master/LICENSE).

## Author
Jesus Guerrero
- website: [jesusantguerrero.com](https://jesusantguerrero.com)
- twitter: [@jesusntguerrero](https://twitter.com/jesusntguerrero) 
