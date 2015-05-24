import Tkinter as tk
import glob
import picamera
import MySQLdb
import smtplib
import serial
from email.MIMEMultipart import MIMEMultipart
from email.MIMEBase import MIMEBase
from email.MIMEText import MIMEText
from email.Utils import COMMASPACE, formatdate
from email import Encoders
import os
import datetime
import time
from datetime import datetime
from PIL import Image, ImageTk

global path,status,pictures,settings,db_user,db_password,mail_password,countdown_start,slide_duration,greet_time,value#program-specific variables
global text_size, label_length,screen_size,picture_size,background,foreground #UI-specific variables
global effects,selected_effect,resolution,annotate_size#camera-related variables
global slide_status,current_picture#slide-related variables
global mail,social#user-provided variables
global camera,serial#functional objects

########CUSTOMIZABLE VARIABLES########
path="/var/www/randaps/"#Path of the RandA-PhotoSharing system
effects=["none","cartoon","colorswap","emboss","negative","oilpaint","sketch"]#Selectable effects
resolution=(1280,720)#High-res image resolution
db_user="randaps"#Database username
db_password="randaps"#Database password
mail_password="INSERT_YOUR_EMAIL_PASSWORD"#Email password
countdown_start="5"#Countdown starting number
slide_duration=5000#Onscreen time of a slide on the start screen in milliseconds
background="white"#Background color
foreground="blue"#Foreground color (for labels)
annotate_size=70#Annotation size on the camera preview
greet_time=10000#Minimum duration in milliseconds of the by
#####################################

settings={}
status=0
selected_effect=0
slide_status=0
current_picture=0
social=False


class Fullscreen_Window:

        def __init__(self):
		global pictures,camera,mail,serial,path,background,annotate_size
                self.tk = tk.Tk()
		self.tk.wm_title("RandA-PhotoSharing")
		self.tk.configure(background=background)
                self.tk.attributes('-zoomed', True)  # This just maximizes it so we can see the window. It's nothing to do with fullscreen.
		#initialize camera
		camera=picamera.PiCamera()
		camera.annotate_text_size=annotate_size
		camera.led=False
		pictures=glob.glob(path+"photos/*.gif")
		self.initialize_ui()
		self.state = False
                self.tk.bind("<F11>", self.toggle_fullscreen)
                self.toggle_fullscreen()

        def toggle_fullscreen(self, event=None):
                self.state = not self.state  # Just toggling the boolean
                self.tk.attributes("-fullscreen", self.state)
                return "break"

	def initialize_ui(self):
		global label_length,screen_size,text_size,picture_size,countdown_start,background,foreground
		#Initialize UI-specific variables
		screen_size=(self.tk.winfo_screenwidth(),self.tk.winfo_screenheight())
		picture_size=(int(screen_size[0]/1.5),int(screen_size[1]/1.5))
			
		label_length=int(screen_size[0]*0.92)
		text_size=int(screen_size[1]/20)

		pad=int(screen_size[1]/36)
		#FIRST
		#create a general container
		self.first_frame=tk.Frame(self.tk,width=label_length)
		self.first_frame.configure(background=background)		
		#create image label
		self.ff_photolabel=tk.Label(self.first_frame)
		#self.ff_photolabel.image=photo
		self.ff_photolabel.pack()
		#create a text label
		self.ff_start=tk.StringVar()
		self.ff_textlabel=tk.Label(self.first_frame,textvariable=self.ff_start,font=("Helvetica",text_size),fg=foreground,bg=background,wraplength=label_length)
		self.ff_textlabel.pack()		
		#THIRD
		#create a general container
		self.third_frame=tk.Frame(self.tk,width=label_length)
		self.third_frame.configure(background=background)
		#create the text label
		self.tf_text=tk.StringVar()
		self.tf_label=tk.Label(self.third_frame,textvariable=self.tf_text,font=("Helvetica",text_size),fg=foreground,bg=background,wraplength=label_length)
		self.tf_label.pack(pady=pad)
		#create countdown label
		self.tf_countdown=tk.StringVar()
		self.tf_countdown.set(countdown_start)
		self.tf_countdownlabel=tk.Label(self.third_frame,textvariable=self.tf_countdown,font=("Helvetica",text_size*4),fg=foreground,bg=background,wraplength=label_length)
		self.tf_countdownlabel.pack(pady=pad*4)
		#FOURTH
		#create a general container
		self.fourth_frame=tk.Frame(self.tk,width=label_length)
		self.fourth_frame.configure(background=background)
		#create image label
		self.ftf_photolabel=tk.Label(self.fourth_frame)
		#self.ftf_photolabel.image=photo
		self.ftf_photolabel.pack(pady=pad)
		#create a text label
		self.ftf_text=tk.StringVar()
		self.ftf_textlabel=tk.Label(self.fourth_frame,textvariable=self.ftf_text, font=("Helvetica",text_size),fg=foreground,bg=background,wraplength=label_length)
		self.ftf_textlabel.pack()
		#create yes label
		self.ftf_yes=tk.StringVar()
		self.ftf_yeslabel=tk.Label(self.fourth_frame,textvariable=self.ftf_yes,font=("Helvetica",text_size),fg=foreground,bg=background,wraplength=label_length)
		#create no label
		self.ftf_no=tk.StringVar()
		self.ftf_nolabel=tk.Label(self.fourth_frame,textvariable=self.ftf_no,font=("Helvetica",text_size),fg=foreground,bg=background,wraplength=label_length)
		#email entry
		self.email=tk.StringVar()
		self.ftf_entry=tk.Entry(self.fourth_frame,textvariable=self.email,font=("Helvetica",text_size))
		self.ftf_entry.bind("<Return>", self.enter_fifth)

		self.first_frame.pack()
		self.init_first() #start the first mode		
		return

	def enter_fifth(self):
		self.ftf_entry.pack_forget()
		self.init_seventh()

	def load_settings(self):
		global settings,path,pictures,picture_size,db_user,db_password
		
		database=MySQLdb.connect("localhost",db_user,db_password,db_user)
		sql="SELECT Tag,Value FROM settings"
		cursor=database.cursor()
		try:
			cursor.execute(sql)
			rows=cursor.fetchall()
			for row in rows:
				settings[row[0]]=row[1]
		except:
			print "Error: unable to fetch data"
		database.close()
		self.ff_start.set(settings["text_start"])#Start
		self.tf_text.set(settings["text_measurement"])#Measuring
		self.ftf_text.set(settings["text_social"])#Social disclaimer
		self.ftf_yes.set(settings["text_yes"])#Yes label
		self.ftf_no.set(settings["text_no"])#No label


		if not(Image.open(path+"data/"+settings["theme"]+"_screen.gif").size==screen_size or (os.path.isfile(path+"data/"+settings["theme"]+"_screen_tmp.gif") and Image.open(path+"data/"+settings["theme"]+"_screen_tmp.gif").size==screen_size)):
			command="/usr/bin/convert "+path+"data/"+settings["theme"]+"_screen.gif -resize "+str(screen_size[0])+"x"
			command+=str(screen_size[1])+" "+path+"data/"+settings["theme"]+"_screen_tmp.gif"
			os.system(command)

		if not(Image.open(path+"data/"+settings["theme"]+"_end.gif").size==screen_size or (os.path.isfile(path+"data/"+settings["theme"]+"_end_tmp.gif") and Image.open(path+"data/"+settings["theme"]+"_end_tmp.gif").size==screen_size)):
			command="/usr/bin/convert "+path+"data/"+settings["theme"]+"_end.gif -resize "+str(screen_size[0])+"x"
			command+=str(screen_size[1])+" "+path+"data/"+settings["theme"]+"_end_tmp.gif"
			os.system(command)
	
                pictures=glob.glob(path+"photos/*.gif")

	def slide_image(self):
		global current_picture, pictures, settings,path,screen_size
		photo=None
		if ( len(pictures) !=0 and settings["standby"]!="0"):
			current_picture=(current_picture+1)%len(pictures)
			photo=tk.PhotoImage(file=pictures[current_picture])
		else:
			if Image.open(path+"data/"+settings["theme"]+"_screen.gif").size==screen_size:
				photo=tk.PhotoImage(file=path+"data/"+settings["theme"]+"_screen.gif")	
			else:	
				photo=tk.PhotoImage(file=path+"data/"+settings["theme"]+"_screen_tmp.gif")
		self.ff_photolabel.configure(image=photo)
		self.ff_photolabel.image=photo

	def clear_input(self):
		global serial
		val = "1"
		while val!="9":
			serial.write('1')
			val=serial.readline()
			val=val.strip()
		
	def init_first(self):
		global status
		self.clear_input()
		self.load_settings()
		self.slide_image()
		status=1
		return
	def init_second(self):
		global status,settings
		status=2
		camera.annotate_text=settings["text_preview"]
		camera.start_preview()
	def init_third(self):
		global status
		status=3
		self.third_frame.pack()
	def init_fourth(self):
		global status
		status=4
		self.ftf_yeslabel.pack(side=tk.LEFT)
		self.ftf_nolabel.pack(side=tk.RIGHT)
	def init_fifth(self):
		global status	
		status=5
		self.ftf_text.set(settings["text_email"])#Email disclaimer
		self.ftf_entry.pack()
		self.ftf_entry.focus()
	def init_sixth(self):
		global status
		status=6
		self.ftf_text.set(settings["text_end"])	
	def init_seventh(self):
		global status,settings
		status=7
		self.fourth_frame.pack_forget()
		photo=None
		if Image.open(path+"data/"+settings["theme"]+"_screen.gif").size==screen_size:
			photo=tk.PhotoImage(file=path+"data/"+settings["theme"]+"_end.gif")	
		else:	
			photo=tk.PhotoImage(file=path+"data/"+settings["theme"]+"_end_tmp.gif")
		self.ff_photolabel.configure(image=photo)
		self.ff_photolabel.image=photo
		self.first_frame.pack()

#initialize serial
serial= serial.Serial('/dev/ttyS0',9600)
w = Fullscreen_Window()

def change_effect(direction):
	global effects,selected_effect
	if direction:
		selected_effect=(selected_effect+1)%len(effects)
	else:
		selected_effect=(selected_effect-1)%len(effects)
	camera.image_effect=effects[selected_effect]

def take_picture():
	global pictures,selected_effect,effects,path,resolution

	i=datetime.now()
	now=i.strftime('%Y%m%d-%H%M%S')

	camera.led= True
	camera.resolution=resolution
	camera.capture(path+"photos/"+now+".jpg")
	camera.led= False	

	pictures.append(path+"photos/"+now)#append it without format to reduce further string operations to work on multiple formats
					#it will be synced on load_settings when the list of the pictures will be updated from the folder photos/
	selected_effect=0
	camera.image_effect=effects[selected_effect]
	time.sleep(1)	

def manipulate_picture():
	global pictures,settings,path,value,picture_size

	begin="/usr/bin/convert "+pictures[-1]+".jpg "
	command=""
	end=pictures[-1]+".jpg"

	if os.path.isfile(path+"data/logo.png"):#If there's a logo to apply
		command=path+"data/logo.png -geometry +"+str(settings["logo_x"])+"+"+str(settings["logo_y"])+" -composite "
		os.system(begin+command+end)

	if os.path.isfile(path+"data/"+settings["theme"]+"_overlay.png"):#If there's an overlay to apply
		command=path+"data/"+settings["theme"]+"_overlay.png -geometry +"+str(settings["overlay_x"])+"+"+str(settings["overlay_y"])+" -composite "
		os.system(begin+command+end)
	
	#Annotate the text
	command="-pointsize 48 -fill white -annotate +"+str(settings["result_x"])
	command+="+"+str(settings["result_y"]) +" '"+str(value)+"%' "
	os.system(begin+command+end)
	value=0 #reset the value

	#rescale in gif format
	command="/usr/bin/convert "+path+"photos/"+now+".jpg -resize "+str(picture_size[0])+"x"
	command+=str(picture_size[1])+" "+path+"photos/"+now+".gif"
	os.system(command)

def send_mail(to, subject,photo):
	global settings,mail,mail_password
	assert type(to)==list
	try:
		msg = MIMEMultipart('alternative')
		msg['From'] = settings["sender_email"]
		msg['To'] = COMMASPACE.join(to)
		msg['Date'] = formatdate(localtime=True)
		msg['Subject'] = subject
   
    		# Create the body of the message (a plain-text and an HTML version).
		text = "Thank you to try our RandA Photo Sharing\n"
		html = settings["email_body"]
	  
    		# Record the MIME types of both parts - text/plain and text/html.
		part1 = MIMEText(text, 'plain')
		part2 = MIMEText(html, 'html')
 
		# Attach parts into message container.
		# According to RFC 2046, the last part of a multipart message, in this case
		# the HTML message, is best and preferred.
		msg.attach(part1)
		msg.attach(part2)
	     
		part = MIMEBase('application', "octet-stream")
		part.set_payload( open(photo,"rb").read() )
		Encoders.encode_base64(part)
		part.add_header('Content-Disposition', 'attachment; filename="%s"'
		% os.path.basename(photo))
		msg.attach(part)
	     
		#initialize mail provider
		mail = smtplib.SMTP("smtp.gmail.com",587)
		mail.starttls()
		mail.login(settings["sender_email"],mail_password)
		mail.sendmail(settings["sender_email"], to, msg.as_string())
		mail.quit()
	except:
		print "Impossibile inviare la mail"


def end_loop():
	global settings,social,pictures,path
	to=[]
	subj=settings["email_customobject"]
	pic=pictures[-1]+".jpg"
	if w.email.get():
		to.append(w.email.get())
	if social==True:
		to.append("trigger@recipe.ifttt.com")
		subj+=" "+settings["email_object"]

	if settings["photo_cloud"]=="1":
		to.append(settings["sender_email"])

	if to:
		send_mail(to,subj,pic)


	if settings["photo_save"]=="0":
		os.remove(pictures[-1]+".gif")
		os.remove(pictures[-1]+".jpg")
		pic=""

	val=0
	if social==True:
		val=1
	database=MySQLdb.connect("localhost",db_user,db_password,db_user)
	cursor=database.cursor()
	pic=pic.split('/')[-1]
	sql = "INSERT INTO photos(File,Social,eMail) VALUES ('"+pic+"','"+str(val)+"','"+w.email.get()+"')"

	try:
		cursor.execute(sql)
		database.commit()
	except:
		database.rollback()

	w.email.set("")
	social=False
	

def getInput():
	global serial
	serial.write('1')# Listen to buttons state
	val=serial.readline()
	return val.strip()
def getMeasure():
	global serial
	value=""
	serial.write('2')# Listen to measurement state
	value=serial.readline()
	value=value.strip()
	return value

def manipulate_and_return():
	manipulate_picture()
	w.third_frame.pack_forget()
	photo=tk.PhotoImage(file=pictures[-1]+".gif")

	w.tf_countdown.set(countdown_start)
	w.ftf_photolabel.configure(image=photo)
	w.ftf_photolabel.image=photo
	w.fourth_frame.pack()
	if settings["photo_social"]=="1":
		w.init_fourth()
	elif settings["photo_email"]=="1":
		w.init_fifth()
	else:
		w.init_sixth()

	w.tk.after(50,checks)
	return

def countdown():
	global settings,countdown_start,value
	if w.tf_countdown.get()==settings["text_photo"]:
		take_picture()
		w.countdown.set(settings["text_wait"]
		w.tk.after(50,manipulate_and_return)
		return
	count=int(w.tf_countdown.get())
	if count >1:
		w.tf_countdown.set(str(count-1))
		w.tk.after(1000,countdown)
	elif count==1:
		w.tf_countdown.set(settings["text_photo"])
		value=getMeasure()
		value= (int(value)*100)/1024
		w.tk.after(1000,countdown)
		

def checks():
	global status,pictures,slide_status,selected_effect,settings,social,camera,path,slide_duration,greet_time
	#Status specific checks
	if status==1:
		slide_status = (slide_status+1)% int(slide_duration/50)
		if slide_status == 0:
			w.slide_image()
	elif status==3:
		w.tk.after(1000,countdown)		
		return
	elif status==7:
			a=datetime.now()
			end_loop()
			b=datetime.now()
			elapsed=b-a
			elapsed_millis=(elapsed.seconds*1000)+int(elapsed.microseconds/1000)
			if elapsed_millis<greet_time:
				time.sleep( (greet_time-elapsed_millis)/1000)	
			w.init_first()
	
	#Input specific checks	
	inp=getInput()
	if inp!="9":
		if status==1 and inp=="2":
			w.first_frame.pack_forget()
			w.init_second()
		elif status==2:
			if inp=="0":
				change_effect(False)
			elif inp=="1":
				change_effect(True)
			else:
				camera.annotate_text=""
				camera.stop_preview()
				w.init_third()
		elif status==4:
			if inp=="0" or inp=="1":
				social= (inp=="0")
				w.ftf_yeslabel.pack_forget()
				w.ftf_nolabel.pack_forget()
				if settings["photo_email"]=="1":
					w.init_fifth()
				else:
					w.init_seventh()
		elif status==5:
			w.ftf_entry.pack_forget()
			w.init_seventh()
		elif status==6:
			if inp=="2" :
				w.init_seventh()
	w.tk.after(50,checks)

w.tk.after(50,checks)
w.tk.mainloop()
