package Model.GraphicObjects
{
  import Model.CourtObject;
  
  import mx.controls.Image;
  import mx.core.Container;
	
	//нападающий
	public class Forward extends CourtObject
	{
	public static var Images:Array=new Array(""
											,"Model/Img/forwards/f01.png"
	   										,"Model/Img/forwards/f02.png"
    										,"Model/Img/forwards/f03.png"  
    										,"Model/Img/forwards/f04.png"
    										,"Model/Img/forwards/f05.png"
    										,"Model/Img/forwards/f06.png"
    										,"Model/Img/forwards/f07.png"
    										,"Model/Img/forwards/f08.png"
    										,"Model/Img/forwards/f09.png");  
    //путь к изображению
    public var pathImage:String = "";
		
		public var forwardType:int = 1;
		
	  public function Forward(fType : int = 1)
	  {
	  	super();
	  	
	  	forwardType = fType;
	  	obj.source = Images[fType];
      }
	  
	  override public function get XPos():int  {  return obj.x;    } 
    override public function get YPos():int  {  return obj.y;    } 
    override public function set XPos(val:int):void  {  obj.x = val;    } 
    override public function set YPos(val:int):void  {  obj.y = val;    } 
	  override public function get image():Image { return obj; }
	      
	  override public function AddObjectOnFrame(parent:Container):void
	  {
	  	obj.x = XPos;
	  	obj.y = YPos;
	  	
	  	parent.addChild(obj);
	  }
	  
	  override public function CloneCourtOject():CourtObject
    {
      var clonedItem:Forward = new Forward(this.forwardType);
            
      clonedItem.color = this.color;
      clonedItem.guid = this.guid;
      clonedItem.Height = this.Height;
      clonedItem.Width = this.Width;
      clonedItem.XPos = this.XPos;
      clonedItem.YPos = this.YPos;
      clonedItem.forwardType = this.forwardType;
      var clonedObject:Image = new Image();
      clonedObject.x = this.obj.x;
      clonedObject.y = this.obj.y;
      clonedObject.source = this.obj.source;      
      clonedItem.obj = clonedObject;
      return clonedItem as CourtObject;
    }
    
  
    override public function Save():XML
    {
       var s:XML = <Player type={this.forwardType} color={this.color} x={XPos} y={YPos} width={this.Width} height={this.Height}>
       				<Image source={this.obj.source} x={this.obj.x} y={this.obj.y} />
       				<HashCode id={this.guid} />
       			   </Player>;
       return s;	
    }
    
    override public function Load(serializedObject:XML):CourtObject
    {
       var so:Forward = new Forward(serializedObject.type);	
       so.forwardType = int(serializedObject.type);
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