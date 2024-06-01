# Instructions

To run the GUI application, you first need to install python version 3.11.3 and all the dependencies. Follow these steps:

**1. Install Python 3.11.3:**
Open your PowerShell terminal and install python:
```powershell
Invoke-WebRequest -Uri "https://www.python.org/ftp/python/3.11.3/python-3.11.3-amd64.exe" -OutFile "python-3.11.3-amd64.exe"
```

**2. Navigate to the Project Directory:** <br><br>
Change to the project directory:
```cmd
cd <path to repo>
```
**3. Install Dependencies:** <br><br>
Install the required dependencies by running:
```cmd
python -r requirements.txt
```

**4. Run the database**
To make the application work, you need to install:

<a href="https://www.wampserver.com/en/download-wampserver-64bits/"><img width="48" height="48" src="https://upload.wikimedia.org/wikipedia/commons/thumb/f/f4/WampServer-logo.svg/1200px-WampServer-logo.svg.png"/></a>
<a href="https://dev.mysql.com/downloads/workbench/" target="_blank"><img height="48" width="48" src="https://upload.wikimedia.org/wikipedia/commons/thumb/0/0e/Antu_mysql-workbench.svg/2048px-Antu_mysql-workbench.svg.png"/></a>

After installing these three softwares, start the WAMP SERVER software to launch a local server on your machine.

You should see the WAMP logo at the bottom right; wait until it turns green. (If the logo does not turn green, click on it and restart its services.)

![rectangle](https://github.com/Githendra23/NextBuilder-Connect/assets/51377697/195adc12-f300-4df8-b535-84f50af1061f)

Then, open the file "**BDD_e-commerce.mwb**" located in the folder *Student 1/database*.

By clicking on the file, you will open the MySQL Workbench software. In the software, click on *Database -> Forward engineer...* in the menu bar.

![rectangle](https://github.com/Githendra23/NextBuilder-Connect/assets/51377697/b2604c76-8d74-406e-b2bd-6439343e5f7b)

Fill in the information displayed in the image below.

![image](https://github.com/Githendra23/NextBuilder-Connect/assets/51377697/4790347f-3921-4892-9654-7f6f31101368)

Click on the Next button, continuing to click until everything is correct, then close the software. <br>
You have now installed the database on your local server on your machine.

**4. Add products to the database**
If you run the application it would not show any products because the products table is empty.
To add products, open your browser and search:
```
http://localhost/phpmyadmin/
```
To login, put *root* as the username (no password needed)

Go to the *site_e-commerce* and click the products table.

Go to the SQL tab bar and add:
```sql
INSERT INTO `produit` (`id`, `NOM_PRODUIT`, `PRIX_UNITAIRE`, `POIDS`, `STOCK`, `TYPE`, `URL`) VALUES
(1, 'Concombre', 1, 1, 200, 'périssables', 'https://static.wikia.nocookie.net/house-party/images/5/59/Cucumber.png'),
(2, 'Tomate', 1, 1, 200, 'périssables', 'https://pngimg.com/d/tomato_PNG12511.png'),
(3, 'Oignon', 1, 1, 200, 'périssables', 'https://pngimg.com/d/onion_PNG99190.png'),
(4, 'Carrote', 1, 1, 200, 'périssables', 'https://www.transparentpng.com/thumb/carrot/AciY35-carrot-transparent-picture.png'),
(5, 'Pommes de terre', 1, 1, 200, 'périssables', 'https://www.lespommesdeterre.com/wp-content/themes/cnipt-theme/img/pages/agata.png'),
(6, 'Pomme', 1, 1, 200, 'périssables', 'https://freepngimg.com/save/4495-apple-png-image/500x490')
```

And press **Go**

**5. Run the Application:** <br><br>
Finally, run the application with:
```cmd
python barcode_weighing_scale.py
```
