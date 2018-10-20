package Model.GraphicObjects
{
  import Model.CourtObject;
  
  import mx.controls.Image;
  import mx.core.Container;
  
  // Игрок текущей команды.
  public class Player extends CourtObject
  {
  	public static var Images:Array = new Array("",
     "Model/Img/players/01.png",
     "Model/Img/players/02.png",
	   "Model/Img/players/03.png",
	   "Model/Img/players/04.png",
	   "Model/Img/players/05.png",
	   "Model/Img/players/06.png",
	   "Model/Img/players/07.png",
	   "Model/Img/players/08.png",
	   "Model/Img/players/09.png",
	   "Model/Img/players/o1.png",
     "Model/Img/players/o2.png",
     "Model/Img/players/o3.png",
     "Model/Img/players/o4.png",
     "Model/Img/players/o5.png",
     "Model/Img/players/o6.png",
     "Model/Img/players/o7.png",
     "Model/Img/players/o8.png",
     "Model/Img/players/o9.png",
     "Model/Img/players/x1.png",
     "Model/Img/players/x2.png",
     "Model/Img/players/x3.png",
     "Model/Img/players/x4.png",
     "Model/Img/players/x5.png",
     "Model/Img/players/x6.png",
     "Model/Img/players/x7.png",
     "Model/Img/players/x8.png",
     "Model/Img/players/x9.png",
     "Model/Img/players/v1.png",
     "Model/Img/players/v2.png",
     "Model/Img/players/v3.png",
     "Model/Img/players/v4.png",
     "Model/Img/players/v5.png",
     "Model/Img/players/v6.png",
     "Model/Img/players/v7.png",
     "Model/Img/players/v8.png",
     "Model/Img/players/v9.png"     );

        
    public var playerType:int = 1;
    
    //путь к изображению
    //public var pathImage:String = "";
    
    public var arrayPathImgPlayers:Array = new Array();
    
    override public function get XPos():int  {  return obj.x;    } 
    override public function get YPos():int  {  return obj.y;    } 
    override public function set XPos(val:int):void  {  obj.x = val;    } 
    override public function set YPos(val:int):void  {  obj.y = val;    }
    
    override public function get image():Image { return obj; }
  
    public function Player(pType:int=1)
    {
      super();
      
      arrayPathImgPlayers = Images;
      playerType = pType;
      obj.autoLoad = true;
      // TODO: Обработка ошибок!!!  
      obj.source = arrayPathImgPlayers[playerType];
                                                                                                      
    }
    
    override public function AddObjectOnFrame(parent:Container):void 
    {
      //obj.width = super.Width != 0 ? super.Width : 50;
      //obj.height = super.Height != 0 ? super.Height : 50;
      
      obj.x = XPos;
      obj.y = YPos;
      
      parent.addChild(obj);
    }   
    
    override public function CloneCourtOject():CourtObject
    {
      var clonedItem:Player = new Player(this.playerType);
      
      clonedItem.color = this.color;
      clonedItem.guid = this.guid;
      clonedItem.Height = this.Height;
      clonedItem.Width = this.Width;
      clonedItem.XPos = this.XPos;
      clonedItem.YPos = this.YPos;
      clonedItem.playerType = this.playerType;
      
      var clonedObject:Image = new Image();
      clonedObject.x = this.obj.x;
      clonedObject.y = this.obj.y;
	  clonedObject.source = this.obj.source;
	  clonedItem.obj = clonedObject;
      return (clonedItem as CourtObject);
    }
    
    override public function Save():XML
    {
       var s:XML = <Player type={this.playerType} color={this.color} x={XPos} y={YPos} width={this.Width} height={this.Height}>
       				<Image source={this.obj.source} x={this.obj.x} y={this.obj.y} />
       				<HashCode id={this.guid} />
       			   </Player>;
       return s;	
    }
    
    override public function Load(serializedObject:XML):CourtObject
    {
       var so:Player = new Player(serializedObject.type);	
       so.playerType = int(serializedObject.type);
       so.color = serializedObject.color;
       so.guid = serializedObject.HashCode.@id.toString();
       so.Height = serializedObject.height;
       so.Width = serializedObject.width;
       so.XPos = serializedObject.x;
       so.YPos = serializedObject.y;
       so.obj.source = serializedObject.Image.@source.toString();
       so.obj.x = serializedObject.Image.@x;
       so.obj.y = serializedObject.Image.@y;
       return so as CourtObject;
    }
  }
}