"""
POS Solution System GUI
Author: Githendra Perera
Version: 4.8
"""

from tkinter import *
from io import BytesIO
from PIL import Image, ImageTk
from CTkMessagebox import CTkMessagebox
import customtkinter as ctk
import mysql.connector, requests, time, subprocess, sys

from printer import Printer
from scale import Scale 

BUTTONS_IN_A_ROW = 3
IMG_SIZE = (50, 50)

class App(ctk.CTk):
    appWidth, appHeight = 460, 215
    rowX, columnY = 2, 0
    numberOfProducts = 0
    dbConnectionState = False
    printer = Printer()
    scale = Scale()

    def __init__(self):
        super().__init__()
        self.idProduct = None

        self.spawn_x = (self.winfo_screenwidth() - self.appWidth)/2
        self.spawn_y = (self.winfo_screenheight() - self.appWidth)/2

        self.lastButtonNumber = 4
        self.fade = 100

        self.transparent_color = self._apply_appearance_mode(self._fg_color)
        self.attributes("-transparentcolor", self.transparent_color)

        self.config(background = self.transparent_color)

        try:
            self.mydb = mysql.connector.connect(
                host = "localhost", #"mysql-projet-e-commerce.alwaysdata.net",
                user = "root", #"312817",
                password = "", #"LucasRatonLaveur",
                database = "site_e-commerce" #"projet-e-commerce_bdd"
            )
            self.mycursor = self.mydb.cursor()
            self.dbConnectionState = True

        except mysql.connector.Error as e:
            print("Error reading data from MySQL table", e)
            self.dbConnectionState = False
            self.overrideredirect(1)
            CTkMessagebox(title="Erreur", message="Échec connexion BDD", icon="cancel")

        if self.dbConnectionState is True:
            self.attributes("-topmost", True)

            self.frame_top = ctk.CTkFrame(self, corner_radius = 10, width = 10, border_width=1)
            self.frame_top.grid(sticky="nswe")

            self.frame_top.bind("<B1-Motion>", self.move_window)
            self.frame_top.bind("<ButtonPress-1>", self.oldxyset)
            self.frame_top.bind("<Map>", self.on_configure)

            self.button_close = ctk.CTkButton(self.frame_top, corner_radius=10, width=10, height=10, hover=False,
                                            text="", fg_color="red", command=self.button_event)
            self.button_close.configure(cursor="arrow")        
            self.button_close.grid(row=0, column=3, sticky="ne", padx=(5, 10), pady=10)
            self.button_close.bind("<Enter>", lambda e, button = self.button_close: self.enter_closeButton(button))
            self.button_close.bind("<Leave>", lambda e, button = self.button_close: self.leave_closeButton(button))

            self.button_minimize = ctk.CTkButton(self.frame_top, corner_radius=10, width=10, height=10, hover=False,
                                            text="", fg_color="orange", command=self.minimize_window)
            self.button_minimize.configure(cursor="arrow")        
            self.button_minimize.grid(row=0, column=2, sticky="ne", padx=5, pady=10)
            
            self.geometry(f'+{self.spawn_x}+{self.spawn_y}')

            # it contains the name of each buttons and its colour
            self.buttonsArr  = [[] for _ in range(2)]
            # it contains the name of the products and their img link 
            self.productsArr = [[] for _ in range(2)]

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
                    self.productsArr[0].append(nameItem)
                    self.numberOfProducts += 1

                # get the url of the picture stored in the database and add it in an array
                self.mycursor.execute(f"SELECT URL FROM produit WHERE id = {i} AND TYPE = 'périssable'")
                url = (str(self.mycursor.fetchone())).replace("('", "").replace("',)", "")
                if url != "None":
                    self.productsArr[1].append(url)
                
            ctk.set_appearance_mode("Dark")
            ctk.set_default_color_theme("green")   

            self.title("EAN-13 Code Barre")

            # Weight Label
            weightLabel = ctk.CTkLabel(self.frame_top,
                                            text = "Poids")
            weightLabel.grid(row = 0, column = 0,
                                padx = (50, 20), pady = (20, 10),
                                sticky = "ew")

            # Weight Entry Field
            self.weightEntry = ctk.CTkEntry(self.frame_top,
                                state = 'disabled')
            self.weightEntry.grid(row = 0, column = 1,
                                columnspan = 1, padx = (20, 20),
                                pady = (20, 10), sticky = "ew")

            # Display the weight Button
            weightButton = ctk.CTkButton(self.frame_top, text = "Afficher", 
                                            command = self.insertWeight)
            weightButton.grid(row = 1, column = 1,
                                            columnspan = 1,
                                            padx = (20, 20), pady = (0, 20),
                                            sticky = "ew")

            # -------- custom product buttons ------------------------------------------------------
            for i in range(self.numberOfProducts):
                
                # buttons and resize images
                img_url = self.productsArr[1][i]
                img = self.load_image_from_url(img_url, IMG_SIZE)
                
                self.button = ctk.CTkButton(self.frame_top,
                                    image = img, text = self.productsArr[0][i],
                                    compound = "top", command = lambda productName = self.productsArr[0][i]: self.get_product_id(productName))

                # detect the mouse hovering the buttons
                self.button.bind("<ButtonPress-1>"  , lambda e, buttons = self.buttonsArr[0], button = self.button:         self.on_click(button, buttons))
                self.button.bind("<Enter>"          , lambda e, buttons = self.buttonsArr[0], button = self.button: self.buttonEnterHover(button, buttons))
                self.button.bind("<Leave>"          , lambda e, buttons = self.buttonsArr[0], button = self.button: self.buttonLeaveHover(button, buttons))
                self.button.bind("<ButtonRelease-1>", lambda e, buttons = self.buttonsArr[0], button = self.button:    self.buttonClicked(button, buttons))

                if i == 0:
                    self.appHeight += 100
                else:
                    if i % BUTTONS_IN_A_ROW == 0:
                        self.rowX += 1
                        self.columnY = 0
                        self.appHeight += 100                 
                
                if i == 0:
                    self.button.grid(column = self.columnY, row = self.rowX, 
                            columnspan = 1, padx = (25, 0), pady = 10)
                else:
                    self.button.grid(column = self.columnY, row = self.rowX, 
                            columnspan = 1, padx = (25 if (i % BUTTONS_IN_A_ROW == 0) else 0, 0), pady = 10)
                
                self.buttonsArr[0].append(self.button)
                self.buttonsArr[1].append("green")
                self.columnY += 1
        
            printButton = ctk.CTkButton(self.frame_top, 
                                            command = self.print_barcode,
                                            text = "Imprimez Code Barre")
            printButton.grid(row = self.rowX + 1, column = 1,
                                            columnspan = 1,
                                            padx = (20, 20), pady = (20, 5),
                                            sticky = "ew")

            quitButton = ctk.CTkButton(self.frame_top, text = "Quitter", 
                                            command = self.button_event)
            quitButton.grid(row = self.rowX + 2, column = 1,
                                            columnspan = 1,
                                            padx = (20, 20), pady = 5,
                                            sticky = "ew")
            
            reloadButton = ctk.CTkButton(self.frame_top, 
                        image = self.load_image_from_url("https://cdn-icons-png.flaticon.com/512/560/560450.png", (20, 20)),
                        text = None,
                        width = 10, height = 10, 
                        command = self.reload)
            reloadButton.grid(row = self.rowX + 2, column = 2,
                                            padx = (20, 20), pady = 5)
            # ---------------------------------------------------------------------------------------

            self.resizable(False, False)
            self.frame_top.configure(width = self.appWidth, height = self.appHeight)

            self.iconphoto(False,
                self.load_image_from_url("https://cdn-icons-png.flaticon.com/512/2432/2432797.png", IMG_SIZE))
            self.idProduct = None

    # ----------- ToolBar buttons -------------------
    def enter_closeButton(self, button):
        button.configure(fg_color = "#c42b1c")
        
    def leave_closeButton(self, button):
        button.configure(fg_color = "red")
    # ---------------------------------------------------

    def oldxyset(self, event):
            self.oldx = event.x
            self.oldy = event.y

    def on_configure(self, event):
        if self.wm_state() != "zoomed":
            self.overrideredirect(1)

    def minimize_window(self):
        self.overrideredirect(0)
        self.iconify()
        
    def move_window(self, event):
        self.overrideredirect(1)
        self.y = event.y_root - self.oldy
        self.x = event.x_root - self.oldx
        self.geometry(f'+{self.x}+{self.y}')

    def button_event(self, event=None):
        if self.fade:
            self.fade_out()
        self.grab_release()
        self.mydb.close()
        self.destroy()
        self.event = event

    def fade_out(self):
        for i in range(100, 0, -10):
            if not self.winfo_exists():
                break
            self.attributes("-alpha", i/100)
            self.update()
            time.sleep(1/self.fade)

    # ------------- Custom Button ------------------------
    
    def buttonLeaveHover(self, button, buttons):  
        for b in buttons:
            checkButtonName = str(b)

            if b == button:
                if self.buttonsArr[1][0 if (checkButtonName == ".!ctkframe.!ctkbutton") else int(checkButtonName.replace(".!ctkframe.!ctkbutton", "")) - self.lastButtonNumber] == "blue":
                    b.configure(fg_color = "#1f6aa5") # blue
                    
    def buttonClicked(self, button, buttons):
        for b in buttons:
            checkButtonName = str(b)
            
            if b == button:
                if self.buttonsArr[1][0 if (checkButtonName == ".!ctkframe.!ctkbutton") else int(checkButtonName.replace(".!ctkframe.!ctkbutton", "")) - self.lastButtonNumber] == "blue":
                    b.configure(fg_color = "#144870") # dark blue

                    # Simulate a button press event to trigger the button's default behavior
                    b.event_generate("<Button-1>")

            else:
                if self.buttonsArr[1][0 if (checkButtonName == ".!ctkframe.!ctkbutton") else int(checkButtonName.replace(".!ctkframe.!ctkbutton", "")) - self.lastButtonNumber] == "blue":
                    b.configure(fg_color = "#1f6aa5") # blue

    def buttonEnterHover(self, button, buttons):
        for b in buttons:
            checkButtonName = str(b)
            
            if b == button:
                if self.buttonsArr[1][0 if (checkButtonName == ".!ctkframe.!ctkbutton") else int(checkButtonName.replace(".!ctkframe.!ctkbutton", "")) - self.lastButtonNumber] == "blue":
                    b.configure(fg_color = "#144870") # dark blue
            else:
                if self.buttonsArr[1][0 if (checkButtonName == ".!ctkframe.!ctkbutton") else int(checkButtonName.replace(".!ctkframe.!ctkbutton", "")) - self.lastButtonNumber] == "blue":
                    b.configure(fg_color = "#1f6aa5") # blue

    def on_click(self, button, buttons):
        for b in buttons:
            checkButtonName = str(b)
            
            if b == button:
                b.configure(fg_color = "#1f6aa5") # blue
                self.buttonsArr[1][0 if (checkButtonName == ".!ctkframe.!ctkbutton") else int(checkButtonName.replace(".!ctkframe.!ctkbutton", "")) - self.lastButtonNumber] = "blue"
                id(self.productsArr[0][0 if (checkButtonName == ".!ctkframe.!ctkbutton") else int(checkButtonName.replace(".!ctkframe.!ctkbutton", "")) - self.lastButtonNumber])

                # Simulate a button press event to trigger the button's default behavior
                b.event_generate("<Button-1>")

            else:
                b.configure(fg_color="#2fa572") # green
                self.buttonsArr[1][0 if (checkButtonName == ".!ctkframe.!ctkbutton") else int(checkButtonName.replace(".!ctkframe.!ctkbutton", "")) - self.lastButtonNumber] = "green"

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
        if product_id is None:
            CTkMessagebox(message = "Choisissez un produit",
                          icon = "warning", option_1 = "Ok")
            return None
        
        elif product_weight is None:
            CTkMessagebox(message = "Récupérez le poids du produit",
                          icon = "warning", option_1 = "Ok")
            return None

        barcode = int("{:06d}{:05d}".format(product_id, product_weight))
        return barcode

    def get_product_id(self, product_name):
        if product_name is not None:
            sql_query = "SELECT id FROM produit WHERE nom_produit = %s"
            self.mycursor.execute(sql_query, (product_name,))

            id = int(str(self.mycursor.fetchone()).replace("(", "").replace(",)", ""))
            
            self.idProduct = id

    def print_barcode(self):
        self.printer.send_command(self.generate_barcode(self.idProduct, self.scale.getWeight()))

    def reload(self):
        self.button_event()
        python = sys.executable
        subprocess.call([python, __file__])
        sys.exit()

if __name__ == "__main__":
    app = App()
    app.mainloop()