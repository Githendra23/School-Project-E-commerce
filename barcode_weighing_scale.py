from tkinter import *
from zebra import Zebra
from io import BytesIO
from PIL import Image, ImageTk
import customtkinter as ctk
import serial, mysql.connector, atexit, requests

weightProduct = None
idProduct = None
appWidth, appHeight = 460, 325
img_size = (50, 50)
rowX, columnY = 2, 0

buttonsArr = [[] for _ in range(2)]
productsTable = [[] for _ in range(2)]

root = ctk.CTk()

class Printer:
    def send_command(self, barcode):

        if barcode is not None:
            label = f"""
            ^XA
            ^FO170,50,^BY3
            ^BEN,110,Y,N
            ^FD{barcode}^FS
            ^XZ
            """

            z = Zebra('ZDesigner GK420d')
            z.output(label)

class Scale:    
    def weight(self):
        global weightProduct
        
        ser = serial.Serial()
        ser.port = "COM1"
        ser.baudrate = 9600
        ser.bytesize = serial.EIGHTBITS
        ser.parity = serial.PARITY_NONE
        ser.stopbits = serial.STOPBITS_ONE
        ser.timeout = 1

        ser.open()

        data = ser.readline().decode('ascii')

        if data:
            if data.strip().find("M") != -1: # == False
                weightProduct = data.strip().replace("M        ","")
                
                weightEntry.configure(state = 'normal', justify = CENTER)
                weightEntry.delete(0, "end")
                weightEntry.insert(1, weightProduct)
                weightEntry.configure(state = 'disabled')
                
                weightProduct = int(weightProduct.replace(" g",""))
            else:
                weightProduct = weightProduct = data.strip().replace("           ","")
                weightEntry.configure(state = 'normal', justify = CENTER)
                weightEntry.delete(0, "end")
                weightEntry.insert(1, weightProduct)
                weightEntry.configure(state = 'disabled')
                
                weightProduct = int(data.strip().replace(" g",""))
        else:
            weightEntry.configure(state = 'normal', justify = CENTER)
            weightEntry.delete(0, "end")
            weightEntry.insert(1, "Erreur")
            weightEntry.configure(state = 'disabled')
            
            weightProduct = None

        ser.close()

class CustomButton:
    def buttonLeaveHover(self, buttons, button):  
        global buttonsArr

        for b in buttons:
            checkButtonName = str(b)

            if b == button:
                if buttonsArr[1][0 if (checkButtonName == ".!ctkbutton") else int(checkButtonName.replace(".!ctkbutton", "")) - 2] == "blue":
                    b.configure(fg_color = "#1f6aa5") # blue
                    
    def buttonClicked(self, buttons, button):
        # global buttonsArr

        for b in buttons:
            checkButtonName = str(b)
            if b == button:
                if buttonsArr[1][0 if (checkButtonName == ".!ctkbutton") else int(checkButtonName.replace(".!ctkbutton", "")) - 2] == "blue":
                    b.configure(fg_color = "#144870") # dark blue
            else:
                if buttonsArr[1][0 if (checkButtonName == ".!ctkbutton") else int(checkButtonName.replace(".!ctkbutton", "")) - 2] == "blue":
                    b.configure(fg_color = "#1f6aa5") # blue

    def buttonEnterHover(self, buttons, button):
        global buttonsArr

        for b in buttons:
            checkButtonName = str(b)
            
            if b == button:
                if buttonsArr[1][0 if (checkButtonName == ".!ctkbutton") else int(checkButtonName.replace(".!ctkbutton", "")) - 2] == "blue":
                    b.configure(fg_color = "#144870") # dark blue
            else:
                if buttonsArr[1][0 if (checkButtonName == ".!ctkbutton") else int(checkButtonName.replace(".!ctkbutton", "")) - 2] == "blue":
                    b.configure(fg_color = "#1f6aa5") # blue

    def on_click(self, buttons, button):
        global buttonsArr
        
        for b in buttons:
            checkButtonName = str(b)
            
            if b == button:
                b.configure(fg_color = "#1f6aa5") # blue
                buttonsArr[1][0 if (checkButtonName == ".!ctkbutton") else int(checkButtonName.replace(".!ctkbutton", "")) - 2] = "blue"
                id(productsTable[0][0 if (checkButtonName == ".!ctkbutton") else int(checkButtonName.replace(".!ctkbutton", "")) - 2])

            else:
                b.configure(fg_color="#2fa572") # green
                buttonsArr[1][0 if (checkButtonName == ".!ctkbutton") else int(checkButtonName.replace(".!ctkbutton", "")) - 2] = "green"

def load_image_from_url(url, new_size):
    response = requests.get(url)
    image = Image.open(BytesIO(response.content))
    image = image.resize(new_size)
    return ImageTk.PhotoImage(image)

def generate_barcode(id, weight):

    if id is None or weight is None:
        return None

    barcode = int("{:06d}{:05d}".format(id, weight))
    print(barcode)
    return barcode

def id(product_name):
    global idProduct
    
    if product_name is not None:
        sql_query = "SELECT id FROM produit WHERE NOM_PRODUIT = %s"
        mycursor.execute(sql_query, (product_name,))

        id = int(str(mycursor.fetchone()).replace("(", "").replace(",)", ""))
        
        idProduct = id      

def print_barcode():
    printer = Printer()
    printer.send_command(generate_barcode(idProduct, weightProduct))

def quit():
    mydb.close()
    root.quit()
    root.destroy()

# execute this function when the window is closed using the red close button
def exit_handler():
    mydb.close()

try:
    mydb = mysql.connector.connect(
    host     = "localhost",
    user     = "root",
    password = "",
    database = "site_e-commerce"
    )
    numberOfProducts = 0
    mycursor = mydb.cursor()

    mycursor.execute("SELECT MIN(id) AS first_id FROM produit;") # first product
    firstID = int(str(mycursor.fetchone()).replace("(", "").replace(",)", ""))

    mycursor.execute("SELECT MAX(id) AS first_id FROM produit;") # last product
    lastID = int(str(mycursor.fetchone()).replace("(", "").replace(",)", ""))

    for i in range(firstID, lastID + 1):
        mycursor.execute(f"SELECT NOM_PRODUIT FROM produit WHERE id = {i} AND TYPE = 'périssables'")
        nameItem = (str(mycursor.fetchone())).replace("('", "").replace("',)", "")
        if nameItem != "None":
            productsTable[0].append(nameItem)
            numberOfProducts += 1

        mycursor.execute(f"SELECT URL FROM produit WHERE id = {i} AND TYPE = 'périssables'")
        url = (str(mycursor.fetchone())).replace("('", "").replace("',)", "")
        if url != "None":
            productsTable[1].append(url)
        
    # Supported modes : Light, Dark, System
    ctk.set_appearance_mode("System")
    
    # Supported themes : green, dark-blue, blue
    ctk.set_default_color_theme("green")   

    root.title("EAN-13 Code Barre")

    # Weight Label
    weightLabel = ctk.CTkLabel(root,
                                    text = "Poids")
    weightLabel.grid(row = 0, column = 0,
                        padx = 20, pady = (20, 10),
                        sticky = "ew")
    # Weight Entry Field
    weightEntry = ctk.CTkEntry(root,
                        state = 'disabled')
    weightEntry.grid(row = 0, column = 1,
                        columnspan = 1, padx = 20,
                        pady = (20, 10), sticky = "ew")

    # Display the weight Button
    scale = Scale()
    quitButton = ctk.CTkButton(root, text = "Afficher", 
                                    command = scale.weight)
    quitButton.grid(row = 1, column = 1,
                                    columnspan = 1,
                                    padx = 20, pady = (0, 20),
                                    sticky = "ew")

    # -------- buttons ------------------------------------------------------
    btn = CustomButton()
    print(numberOfProducts)
    for i in range(numberOfProducts):
        
        # buttons and resize images
        img_url = productsTable[1][i]
        img = load_image_from_url(img_url, img_size)
        
        button = ctk.CTkButton(root,
                            image = img, text = productsTable[0][i],
                            compound = "top", command = id(productsTable[0][i]))
        num = str(button)
        
        # detect the mouse hovering the buttons
        button.bind("<Button-1>"       , lambda e, buttons = buttonsArr[0], button = button:         btn.on_click(buttons, button))
        button.bind("<Enter>"          , lambda e, buttons = buttonsArr[0], button = button: btn.buttonEnterHover(buttons, button))
        button.bind("<Leave>"          , lambda e, buttons = buttonsArr[0], button = button: btn.buttonLeaveHover(buttons, button))
        button.bind("<ButtonRelease-1>", lambda e, buttons = buttonsArr[0], button = button:    btn.buttonClicked(buttons, button))

        if (1 if (i == 0) else i) % 3 == 0:
            rowX += 1
            columnY = 0

        if (1 if (i == 0) else i) % 4 == 0:
            appHeight += 100
        
        button.grid(column = columnY, row = rowX, columnspan = 1,
                    padx = (0, 0), pady = 10)
        buttonsArr[0].append(button)
        buttonsArr[1].append("green")
        columnY += 1

    printButton = ctk.CTkButton(root, 
                                    command = print_barcode,
                                    text = "Imprimez Code Barre")
    printButton.grid(row = rowX + 1, column = 1,
                                    columnspan = 1,
                                    padx = 20, pady = (20, 5),
                                    sticky = "ew")

    quitButton = ctk.CTkButton(root, text = "Quitter", 
                                    command = quit)
    quitButton.grid(row = rowX + 2, column = 1,
                                    columnspan = 1,
                                    padx = 20, pady = 5,
                                    sticky = "ew")
    # ---------------------------------------------------------------------------------------

    root.resizable(False,False)
    atexit.register(exit_handler)
    root.geometry(f"{appWidth}x{appHeight}")
    root.iconphoto(False, load_image_from_url("https://cdn-icons-png.flaticon.com/512/2432/2432797.png", img_size))
    idProduct = None
    root.mainloop()

except mysql.connector.Error as e:
    print("Error reading data from MySQL table", e)
    
    error_window = ctk.CTk()
    
    ctk.set_appearance_mode("System")
    
    # Supported themes : green, dark-blue, blue
    ctk.set_default_color_theme("green")   
    
    appWidth, appHeight = 280, 130

    error_window.title("EAN-13 Code Barre")
    error_window.geometry(f"{appWidth}x{appHeight}")
    
    error_window.title("Error")
    error_window.resizable(False, False)

    weightLabel = ctk.CTkLabel(error_window,
                                    text="Connexion impossible BDD")
    weightLabel.grid(row = 0, column = 0,
                    padx = 65, pady = 20,
                    sticky = "ew")
    
    quitButton = ctk.CTkButton(error_window, text = "Ok", 
                                    command = error_window.destroy)
    quitButton.grid(row = 1, column = 0,
                    columnspan = 2,
                    padx = 70, pady = 5,
                    sticky = "ew")

    error_window.grab_set()
    error_window.mainloop()
