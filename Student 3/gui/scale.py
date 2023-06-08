import serial

class Scale:
    weightProduct = None
    scaleOutput = None

    def __init__(self):
        self.ser = serial.Serial()
        self.ser.port = "COM1"
        self.ser.baudrate = 9600
        self.ser.bytesize = serial.EIGHTBITS
        self.ser.parity = serial.PARITY_NONE
        self.ser.stopbits = serial.STOPBITS_ONE  

    def setWeight(self):
        try:
            self.ser.open()
            self.ser.write(b"s")

            data = self.ser.readline().decode('ascii')

            if data:
                if data.strip().find("M") != -1: # == False
                    self.scaleOutput = data.strip().replace("M        ","")
                    self.weightProduct = int(self.scaleOutput.replace(" g",""))
                else:
                    self.scaleOutput = data.strip().replace("        ","")  
                    self.weightProduct = int(self.scaleOutput.strip().replace(" g",""))
            else:
                self.weightProduct = None
                
        except serial.SerialException as e:
            print(f"Erreur d'ouverture du port serie: {str(e)}")
            self.weightProduct = None

        finally:
            if self.ser.is_open:
                self.ser.close()

    def getWeight(self):
        return self.weightProduct

    def getScaleOutput(self):
        return self.scaleOutput
