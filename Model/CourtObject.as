package Model
{
  import mx.controls.Alert;
  import mx.controls.Image;
  import mx.core.Container;
  
  // базовый класс для всех объектов на корте
  public class CourtObject
  {
    // у каждого объекта должен быть уникальный идентификатор. 
    // это нужно для разных frame. Объект может находиться на каждом 
    // из кадров, а может на каком-то отсутвовать. 
    // Так как анимация есть информация о координатах объекта на текущем и 
    // на следующем кадрах, нам надо иметь возмоность найти объект на любом кадре
    // Искать будем по guid
    public var guid:String = "";
    
    // позиции объекта на поле.
    virtual public function get XPos():int
    {
      Alert.show("TODO override GetXPos!");
      return -1;
    } 
    // позиции объекта на поле.
    virtual public function set XPos(val:int):void
    {
      Alert.show("TODO override SetXPos!");
    } 
    
     
    // позиции объекта на поле.
    virtual public function set YPos(val:int):void
    {
      Alert.show("TODO override SetYPos!");
    }
    // позиции объекта на поле.
    virtual public function get YPos():int
    {
      Alert.show("TODO override GetYPos!");
      return -1;
    } 
     
     
     
    
    virtual public function get image():Image
    {
      Alert.show("TODO override GetGraphicObject!");
      return null;
    }
    
   
    
    // размеры объекта
    public var Width:int = 0;
    public var Height:int = 0;
    
    // рисунок. Картинка, которая отображается на площадке
    protected var obj:Image = new Image();
    
    // Основной цвет линий объекта.
    public var color:String = "";
    
    public function CourtObject()
    {
    }

    
    // Каждый объект должен отрисовать себя. parent.AddChild или чего-нибудь
    // в этом роде 
    virtual public function AddObjectOnFrame(parent:Container):void
    {
      Alert.show("TODO override AddObjectOnFrame!");
    }
    
    virtual public function CloneCourtOject():CourtObject
    {
      Alert.show("TODO override CloneCourtOject!");
      return null;
    }
    
    public virtual function Save():XML
    {
    	Alert.show("Ovverride save object to xml"); 
    	return null;
    }
    
    public virtual function Load(serializedObject:XML):CourtObject
    {
    	Alert.show("Ovverride save object to xml"); 
   		return null; //new XML(this);
    }

  }
}