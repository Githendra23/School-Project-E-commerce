from tkinter import *
from io import BytesIO
from PIL import Image, ImageTk
import customtkinter as ctk
import mysql.connector, atexit, requests

from printer import Printer
from scale import Scale

BUTTONS_IN_A_ROW = 3
IMG_SIZE = (50, 50)

# it contains the name of each buttons and its colour
buttonsArr  = [[] for _ in range(2)]
# it contains the name of the products and their img link 
productsArr = [[] for _ in range(2)]
        
class App(ctk.CTk):
    def __init__(self):
        super().__init__()
        self.lastButtonNumber = 2

        try:
            self.idProduct = None
            self.appWidth, self.appHeight = 460, 215
            self.rowX, self.columnY = 2, 0
            self.numberOfProducts = 0
            
            self.scale = Scale()

            self.mydb = mysql.connector.connect(
            host = "localhost",
            user = "root",
            password = "",
            database = "site_e-commerce"
            )
            self.mycursor = self.mydb.cursor()

            # get the id of the first perishable product from the produit table 
            self.mycursor.execute("SELECT MIN(id) AS first_id FROM produit")
            firstID = int(str(self.mycursor.fetchone()).replace("(", "").replace(",)", ""))

            # get the id of the last perishable product from the produit table 
            self.mycursor.execute("SELECT MAX(id) AS first_id FROM produit;")
            lastID = int(str(self.mycursor.fetchone()).replace("(", "").replace(",)", ""))

            for i in range(firstID, lastID + 1):
                # get the name of the products stored in the database and add it in an array
                self.mycursor.execute(f"SELECT nom_produit FROM produit WHERE id = {i} AND TYPE = 'périssable'")
                nameItem = (str(self.mycursor.fetchone())).replace("('", "").replace("',)", "")
                if nameItem != "None":
                    productsArr[0].append(nameItem)
                    self.numberOfProducts += 1

                # get the url of the picture stored in the database and add it in an array
                self.mycursor.execute(f"SELECT URL FROM produit WHERE id = {i} AND TYPE = 'périssable'")
                url = (str(self.mycursor.fetchone())).replace("('", "").replace("',)", "")
                if url != "None":
                    productsArr[1].append(url)
                
            ctk.set_appearance_mode("Dark")
            ctk.set_default_color_theme("green")   

            self.title("EAN-13 Code Barre")

            # Weight Label
            weightLabel = ctk.CTkLabel(self,
                                            text = "Poids")
            weightLabel.grid(row = 0, column = 0,
                                padx = 20, pady = (20, 10),
                                sticky = "ew")

            # Weight Entry Field
            self.weightEntry = ctk.CTkEntry(self,
                                state = 'disabled')
            self.weightEntry.grid(row = 0, column = 1,
                                columnspan = 1, padx = 20,
                                pady = (20, 10), sticky = "ew")

            # Display the weight Button
            weightButton = ctk.CTkButton(self, text = "Afficher", 
                                            command = self.insertWeight)
            weightButton.grid(row = 1, column = 1,
                                            columnspan = 1,
                                            padx = 20, pady = (0, 20),
                                            sticky = "ew")

            # -------- custom product buttons ------------------------------------------------------
            for i in range(self.numberOfProducts):
                
                # buttons and resize images
                img_url = productsArr[1][i]
                img = self.load_image_from_url(img_url, IMG_SIZE)
                
                button = ctk.CTkButton(self,
                                    image = img, text = productsArr[0][i],
                                    compound = "top", command = lambda productName = productsArr[0][i]: self.get_product_id(productName))

                # detect the mouse hovering the buttons
                button.bind("<Button-1>"       , lambda e, buttons = buttonsArr[0], button = button:         self.on_click(button, buttons))
                button.bind("<Enter>"          , lambda e, buttons = buttonsArr[0], button = button: self.buttonEnterHover(button, buttons))
                button.bind("<Leave>"          , lambda e, buttons = buttonsArr[0], button = button: self.buttonLeaveHover(button, buttons))
                button.bind("<ButtonRelease-1>", lambda e, buttons = buttonsArr[0], button = button:    self.buttonClicked(button, buttons))

                if i == 0:
                    self.appHeight += 100
                else:
                    if i % BUTTONS_IN_A_ROW == 0:
                        self.rowX += 1
                        self.columnY = 0
                        self.appHeight += 100                 
                
                button.grid(column = self.columnY, row = self.rowX, 
                            columnspan = 1, padx = (0, 0), pady = 10)
                buttonsArr[0].append(button)
                buttonsArr[1].append("green")
                self.columnY += 1
        
            printButton = ctk.CTkButton(self, 
                                            command = self.print_barcode,
                                            text = "Imprimez Code Barre")
            printButton.grid(row = self.rowX + 1, column = 1,
                                            columnspan = 1,
                                            padx = 20, pady = (20, 5),
                                            sticky = "ew")

            quitButton = ctk.CTkButton(self, text = "Quitter", 
                                            command = quit)
            quitButton.grid(row = self.rowX + 2, column = 1,
                                            columnspan = 1,
                                            padx = 20, pady = 5,
                                            sticky = "ew")
            
            reloadButton = ctk.CTkButton(self, 
                        image = self.load_image_from_url("https://cdn-icons-png.flaticon.com/512/560/560450.png", (20, 20)),
                        text = None,
                        width = 10, height = 10, 
                        command = self.reload)
            reloadButton.grid(row = self.rowX + 2, column = 2,
                                            padx = 20, pady = 5)
            # ---------------------------------------------------------------------------------------

            self.resizable(False, False)
            atexit.register(self.exit_handler)
            self.geometry(f"{self.appWidth}x{self.appHeight}")
            self.iconphoto(False,
                self.load_image_from_url("https://cdn-icons-png.flaticon.com/512/2432/2432797.png", IMG_SIZE))
            self.idProduct = None

        except mysql.connector.Error as e:
            print("Error reading data from MySQL table", e)
            
            ctk.set_appearance_mode("Dark")
            ctk.set_default_color_theme("green")   
            
            self.appWidth, self.appHeight = 280, 130

            self.title("EAN-13 Code Barre")
            self.title("Error")

            weightLabel = ctk.CTkLabel(self,
                                        text="Connexion impossible BDD")
            weightLabel.grid(row = 0, column = 0,
                            padx = 65, pady = 20,
                            sticky = "ew")
            
            quitButton = ctk.CTkButton(self, text = "Ok", 
                                            command = self.destroy)
            quitButton.grid(row = 1, column = 0,
                            columnspan = 2,
                            padx = 70, pady = 5,
                            sticky = "ew")

            self.grab_set()
            self.geometry(f"{self.appWidth}x{self.appHeight}")
            self.resizable(False, False)
            self.iconphoto(False,
                self.load_image_from_url("https://cdn-icons-png.flaticon.com/512/2432/2432797.png", IMG_SIZE))

    # ------------- Custom Button ------------------------
    
    def buttonLeaveHover(self, button, buttons):  
        global buttonsArr

        for b in buttons:
            checkButtonName = str(b)

            if b == button:
                if buttonsArr[1][0 if (checkButtonName == ".!ctkbutton") else int(checkButtonName.replace(".!ctkbutton", "")) - self.lastButtonNumber] == "blue":
                    b.configure(fg_color = "#1f6aa5") # blue
                    
    def buttonClicked(self, button, buttons):

        for b in buttons:
            checkButtonName = str(b)
            
            if b == button:
                if buttonsArr[1][0 if (checkButtonName == ".!ctkbutton") else int(checkButtonName.replace(".!ctkbutton", "")) - self.lastButtonNumber] == "blue":
                    b.configure(fg_color = "#144870") # dark blue
            else:
                if buttonsArr[1][0 if (checkButtonName == ".!ctkbutton") else int(checkButtonName.replace(".!ctkbutton", "")) - self.lastButtonNumber] == "blue":
                    b.configure(fg_color = "#1f6aa5") # blue

    def buttonEnterHover(self, button, buttons):
        global buttonsArr

        for b in buttons:
            checkButtonName = str(b)
            
            if b == button:
                if buttonsArr[1][0 if (checkButtonName == ".!ctkbutton") else int(checkButtonName.replace(".!ctkbutton", "")) - self.lastButtonNumber] == "blue":
                    b.configure(fg_color = "#144870") # dark blue
            else:
                if buttonsArr[1][0 if (checkButtonName == ".!ctkbutton") else int(checkButtonName.replace(".!ctkbutton", "")) - self.lastButtonNumber] == "blue":
                    b.configure(fg_color = "#1f6aa5") # blue

    def on_click(self, button, buttons):
        global buttonsArr
        
        for b in buttons:
            checkButtonName = str(b)
            
            if b == button:
                b.configure(fg_color = "#1f6aa5") # blue
                buttonsArr[1][0 if (checkButtonName == ".!ctkbutton") else int(checkButtonName.replace(".!ctkbutton", "")) - self.lastButtonNumber] = "blue"
                id(productsArr[0][0 if (checkButtonName == ".!ctkbutton") else int(checkButtonName.replace(".!ctkbutton", "")) - self.lastButtonNumber])

            else:
                b.configure(fg_color="#2fa572") # green
                buttonsArr[1][0 if (checkButtonName == ".!ctkbutton") else int(checkButtonName.replace(".!ctkbutton", "")) - self.lastButtonNumber] = "green"

    # ----------------------------------------------------

    def insertWeight(self):
        self.scale.setWeight()
        text = self.scale.getScaleOutput()

        if text:
            self.weightEntry.configure(state = 'normal', justify = CENTER)
            self.weightEntry.delete(0, "end")
            self.weightEntry.insert(1, text)
            self.weightEntry.configure(state = 'disabled')
        else:
            self.weightEntry.configure(state = 'normal', justify = CENTER)
            self.weightEntry.delete(0, "end")
            self.weightEntry.insert(1, "Erreur")
            self.weightEntry.configure(state = 'disabled')

    def load_image_from_url(self, url, new_size):
        response = requests.get(url)
        image = Image.open(BytesIO(response.content))
        image = image.resize(new_size)
        return ImageTk.PhotoImage(image)

    def generate_barcode(self, product_id, product_weight):

        if product_id is None or product_weight is None:
            return None

        barcode = int("{:06d}{:05d}".format(product_id, product_weight))
        return barcode

    def get_product_id(self, product_name):
        global idProduct

        if product_name is not None:
            sql_query = "SELECT id FROM produit WHERE nom_produit = %s"
            self.mycursor.execute(sql_query, (product_name,))

            id = int(str(self.mycursor.fetchone()).replace("(", "").replace(",)", ""))
            
            idProduct = id

    def print_barcode(self):
        printer = Printer()
        print(idProduct)
        printer.send_command(self.generate_barcode(idProduct, self.scale.getWeight()))

    def quit(self):
        self.mydb.close()
        self.quit()
        self.destroy()

    # execute this function when the window is closed using the red close button
    def exit_handler(self):
        self.mydb.close()

    def reload(self):
        self.destroy()
        app = App()

if __name__ == "__main__":
    app = App()
    app.mainloop()