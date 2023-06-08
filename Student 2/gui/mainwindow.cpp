#include "mainwindow.h"
#include "ui_mainwindow.h"

MainWindow::MainWindow(QWidget *parent) :
    QMainWindow(parent),
    ui(new Ui::MainWindow)
{
    ui->setupUi(this);

    QSqlDatabase db = QSqlDatabase::addDatabase("QMYSQL");

    // set the database connection parameters
    db.setHostName("localhost");
    db.setDatabaseName("site_e-commerce");
    db.setUserName("root");
    db.setPassword("");

    // open the database connection
    if (!db.open()) qDebug() << "Failed to connect to database:" << db.lastError().text();
    else qDebug() << "Connexion réussite";

}

MainWindow::~MainWindow()
{
    delete ui;
}



void MainWindow::on_pushButtonfacture_et_date_clicked()
{
    srand(time(NULL));

    int randomNumber = rand();
    ui->lineEdit_facture->setText(QString::number(randomNumber));
    QDate date = QDate::currentDate();
    ui->lineEdit_date->setText(date.toString(Qt::ISODate));
}

void MainWindow::on_pushButton_save_clicked()
{
    QString fileName = QFileDialog::getSaveFileName(this, "Save to PDF", "", "*.pdf");
    if (fileName.isEmpty()) return;
    if (!fileName.endsWith(".pdf")) fileName += ".pdf";

    QPrinter printer(QPrinter::PrinterResolution);
    printer.setOutputFormat(QPrinter::PdfFormat);
    printer.setOutputFileName(fileName);

    QPainter painter;
    painter.begin(&printer);
    render(&painter);
    painter.end();

    // regarder les police tty ou ttf
}

/*+ "       "+ requete.value("NOM_PRODUIT").toString() + "       "+ requete.value("PRIX_UNITAIRE").toString()+ "       "+  requete.value("POIDS").toString()
           + "      "+ requete.value("STOCK").toString()+ "       "+requete.value("TYPE").toString();*/


void MainWindow::on_pushButton_produit_clicked()
{

    QString codebarre = ui->textEdit_CodeBarre->toPlainText();
    QStringList codebarreList;
    int startIndex = 0;
    int chunkSize = 6;

    while (startIndex < codebarre.length()) {
        QString chunk = codebarre.mid(startIndex, chunkSize);
        codebarreList.append(chunk);
        startIndex += chunkSize;
    }

    /*for (int i = 0; i <= 2; i++) {
        while(i < codebarreList.length() && codebarreList[i]=='0'){
          i++;
        }
        codebarreList.remove(0,i);
    }*/

    codebarreList[0] = codebarreList[0].remove(QRegExp("^[0]*"));
    codebarreList[1] = codebarreList[1].remove(QRegExp("^[0]*"));                 //sert a retirer les 0 devant un chiffre supperieur à 0
    codebarreList[1] = codebarreList[1].remove(codebarreList[1].size() - 1, 1);// sert a enlever le dernier chiffre
    ui->textEdit_CodeBarre->clear();
    qDebug() << codebarreList[0] << endl;
    qDebug() << codebarreList[1] << endl;

    QSqlQuery requete;
    if(requete.exec("SELECT * FROM produit WHERE id = " + codebarreList[0])) {
        qDebug() << "Ok - requete";

        // Boucle qui permet de parcourir les enregistrements renvoyés par la requête
        while(requete.next()) {
            // On accède ici aux différents champs par leurs noms, il est également possible
            // d'y accéder par leur index : requete.value(0)
            qDebug() << requete.value("id") << " " << requete.value("NOM_PRODUIT") << " " << requete.value("PRIX_UNITAIRE") << " "
                     << requete.value("POIDS") << " " << requete.value("STOCK")
                     << requete.value("TYPE");

            QString id = (requete.value("NOM_PRODUIT").toString());
            //ui->textEdit->append(id);
            ui->tableWidget_data->insertRow(0);
            ui->tableWidget_data->setItem(0, 0, new QTableWidgetItem(id));

            QString prix = (requete.value("PRIX_UNITAIRE").toString());
            //ui->textEdit->append(prix);
            ui->tableWidget_data->setItem(0, 1, new QTableWidgetItem(prix));

            ui->tableWidget_data->setItem(0, 2, new QTableWidgetItem(codebarreList[1]));

        }


    }
}
