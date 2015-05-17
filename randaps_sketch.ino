byte pinNext=8;
byte pinOk=9;
byte pinPrev=10;

bool dx=false;
bool sx=false;
bool ok=false;

bool dxTX=false;
bool sxTX=false;
bool okTX=false;

unsigned long value=0;
unsigned int measurements=0;

void setup() {
  Serial.begin(9600);
  pinMode(pinNext,INPUT);
  pinMode(pinPrev,INPUT);
  pinMode(pinOk,INPUT);
  digitalWrite(pinNext, HIGH);  //enable PullUp
  digitalWrite(pinPrev, HIGH);  //enable PullUp
  digitalWrite(pinOk, HIGH);    //enable PullUp
  pinMode(A0,INPUT);
}

void loop(){
    char input = Serial.read();
    if(input=='1'){
        if (dx && !dxTX){
          dx=false;
          dxTX=true;
          Serial.println("1");
        }else if (sx && !sxTX) {
          sx=false;
          sxTX=true;
          Serial.println("0");
        }else if (ok && !okTX) {
          ok=false;
          okTX=true;
          Serial.println("2");
        }else{
          Serial.println("9");
        }
    }else if(input=='2'){
        measureIN();
        int average= value/measurements;
        Serial.println(average);
        value=0;
        measurements=0;
        
    }
    buttons();
    delay(10);
}

void buttons(){
    if(digitalRead(pinNext)==LOW) dxTX=false;
    if(digitalRead(pinNext)==HIGH && !dxTX) dx=true;
    
    if(digitalRead(pinPrev)==LOW) sxTX=false;
    if(digitalRead(pinPrev)==HIGH && !sxTX) sx=true;
    
    if(digitalRead(pinOk)==LOW) okTX=false;    
    if(digitalRead(pinOk)==HIGH && !okTX) ok=true;
}

void measureIN(){
  for ( measurements=0; measurements<10; measurements++){
    int val;
    val=analogRead(A0);
    value+=val;
    delay(20);
  }  
}