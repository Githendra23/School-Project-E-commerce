from zebra import Zebra

class Printer:
    def __init__(self):
        self.z = Zebra('ZDesigner GK420d')

    def send_command(self, barcode):
        if barcode:
            # ZPL command
            label = f"""
            ^XA
            ^FO170,50,^BY3
            ^BEN,110,Y,N
            ^FD{barcode}^FS
            ^XZ
            """

            self.z.output(label)