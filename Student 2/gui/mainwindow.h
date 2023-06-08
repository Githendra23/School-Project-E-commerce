#ifndef MAINWINDOW_H
#define MAINWINDOW_H
#include <QtSql>
#include <QMainWindow>
#include <iostream>
#include <QtWidgets>
#include <QPrinter>
#include <QFile>
#include <QTextStream>
#include <QFileDialog>
#include <QMessageBox>
#include <QString>
#include <QWidget>
#include <cstdlib>
#include <ctime>
#include <string>
#include <iostream>

using namespace std;

namespace Ui {
class MainWindow;
}

class MainWindow : public QMainWindow
{
    Q_OBJECT

public:
    explicit MainWindow(QWidget *parent = 0);
    ~MainWindow();

private slots:

    void on_pushButtonfacture_et_date_clicked();

    void on_pushButton_save_clicked();

    void on_pushButton_produit_clicked();

private:
    Ui::MainWindow *ui;
    QSqlDatabase BDD;
    QString line2;

};

#endif // MAINWINDOW_H
