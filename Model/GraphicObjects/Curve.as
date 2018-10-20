package Model.GraphicObjects
{
  import Model.CourtObject;
  
  import flash.display.Graphics;
  import flash.geom.Point;
  
  import mx.controls.Alert;
  import mx.controls.Image;
  import mx.core.Container;
  import mx.utils.UIDUtil;
  
  public class Curve extends CourtObject
  {
    public var points:Array;
    public var angle:Number;     // угол между основной осью стрелки и рисочки в конце
    public var dash:int;         // длинна рисоки на стрелке    
    public var firstPoint:Point;    
    public var secondPoint:Point;
    public var ArrowPointStart:Point;
    public var ArrowPointFinish:Point; 
    public var DrawAtFirst:Boolean = true; 
    
    public function Curve()
    {
      this.firstPoint = null;
      this.secondPoint = null;      
      this.guid = UIDUtil.createUID(); 
      this.points = new Array();
      this.Width = 300;
      this.Height = 300;
    }
    
    override public function get image():Image 
    { 
      return obj; 
    }    

    override public function AddObjectOnFrame(parent:Container):void 
    {      
      obj.x = XPos;
      obj.y = YPos;      
      parent.addChild(obj);
    }
    
   public function DrawCurve():void
   {
     obj.graphics.lineStyle(3,0);    
     if(points.length > 2)
     {
       if(((points.length - 2) % 2)==0)
       {
         for(var i:int =0; i < points.length - 3;)
         {
           obj.graphics.moveTo(points[i].x,points[i].y);
           i++;
           var endPoint:Point = points[i];
           i++;  
           obj.graphics.curveTo(endPoint.x, endPoint.y, points[i].x, points[i].y);   
         }
       }
       else
       {
         for(i =0; i < points.length - 3;)
         {
           obj.graphics.moveTo(points[i].x,points[i].y);
           i++;
           endPoint = points[i];
           i++;  
           obj.graphics.curveTo(endPoint.x, endPoint.y, points[i].x, points[i].y);   
         }
         obj.graphics.moveTo(points[i].x,points[i].y);
         i++;
         endPoint = points[i];
         i++;  
         obj.graphics.lineTo(endPoint.x, endPoint.y);
       }
       angle = Math.PI/9;
       dash = 10;
       ArrowPointStart = points[points.length-3];
       ArrowPointFinish = points[points.length-1];

              
     }
     else
     {
      //рисуем прямую
      obj.graphics.moveTo(points[i].x,points[i].y);
      i++;
      endPoint = points[i];
      i++;
      obj.graphics.lineTo(endPoint.x, endPoint.y);     
    }  
    if(DrawAtFirst) 
    {
      DrawAtFirst = false;
      DrawLineArrow(ArrowPointStart,ArrowPointFinish,angle,dash);//рисуем черточки 
    }

  }  
  
 

  
  override public function get XPos():int  {  return obj.x;    } 
  override public function get YPos():int  {  return obj.y;    } 
  override public function set XPos(val:int):void  {  obj.x = val;    } 
  override public function set YPos(val:int):void  {  obj.y = val;    } 
      
  public function DrawLineArrow(p1:Point, p2:Point, alfa:Number, r1:Number):void
  {
     //alfa угол между основной осью стрелки и рисочки в конце
     //r1 длинна риски
     //beta это арктангенс от x/y (width/height)
     //obj.graphics.lineStyle(3,0);
    var beta:Number = Math.atan2(Math.abs(p1.y-p2.y), Math.abs(p1.x-p2.x));
    if (((p2.y-p1.y)!=0)||((p2.x-p1.x)!=0))
    {//первая четверть
      if ((p2.x>=p1.x) && (p2.y<=p1.y))
      { 
        var xl:Number = p2.x  - obj.x - r1 * Math.cos(beta-alfa);
        var yl:Number =  p2.y  - obj.y + r1 * Math.sin(beta-alfa);
        obj.graphics.moveTo(p2.x - obj.x, p2.y - obj.y);  
        obj.graphics.lineTo(xl, yl);
        //координаты правой риски 
        var xr:Number =  p2.x  - obj.x- r1 * Math.cos(beta+alfa);
        var yr:Number =  p2.y  - obj.y + r1 * Math.sin(beta+alfa);
        obj.graphics.moveTo(p2.x - obj.x, p2.y - obj.y);
        obj.graphics.lineTo(xr, yr);       
      }
      //ворая четверть
      if ((p2.x<p1.x) && (p2.y<=p1.y))
      { 
        xl = p2.x  - obj.x + r1 * Math.cos(beta-alfa);
        yl =  p2.y  - obj.y + r1 * Math.sin(beta-alfa);
        obj.graphics.moveTo(p2.x - obj.x, p2.y - obj.y);  
        obj.graphics.lineTo(xl, yl);
        //координаты правой риски 
        xr =  p2.x  - obj.x + r1 * Math.cos(beta+alfa);
        yr =  p2.y  - obj.y + r1 * Math.sin(beta+alfa);
        obj.graphics.moveTo(p2.x - obj.x, p2.y - obj.y);
        obj.graphics.lineTo(xr, yr);          
      }        
      //третья четверть
      if ((p2.x<p1.x) && (p2.y>p1.y))
      { 
        xl = p2.x  - obj.x + r1 * Math.cos(beta-alfa);
        yl =  p2.y  - obj.y - r1 * Math.sin(beta-alfa);
        obj.graphics.moveTo(p2.x - obj.x, p2.y - obj.y);  
        obj.graphics.lineTo(xl, yl);
        //координаты правой риски 
        xr =  p2.x  - obj.x + r1 * Math.cos(beta+alfa);
        yr =  p2.y  - obj.y - r1 * Math.sin(beta+alfa);
        obj.graphics.moveTo(p2.x - obj.x, p2.y - obj.y);
        obj.graphics.lineTo(xr, yr);    
      }
      //четвертая четверть
      if ((p2.x>=p1.x) && (p2.y>=p1.y))
      { 
        xl = p2.x  - obj.x - r1 * Math.cos(beta-alfa);
        yl=  p2.y  - obj.y - r1 * Math.sin(beta-alfa);
        obj.graphics.moveTo(p2.x - obj.x, p2.y - obj.y);  
        obj.graphics.lineTo(xl, yl);
        //координаты правой риски 
        xr =  p2.x  - obj.x - r1 * Math.cos(beta+alfa);
        yr =  p2.y  - obj.y - r1 * Math.sin(beta+alfa);
        obj.graphics.moveTo(p2.x - obj.x, p2.y - obj.y);
        obj.graphics.lineTo(xr, yr);    
            
      }    
    }       
  }    
  
    override public function CloneCourtOject():CourtObject
    {
      var clonedItem:Curve = new Curve();
      
      clonedItem.guid = this.guid;
      clonedItem.Height = this.Height;
      clonedItem.Width = this.Width;
      clonedItem.XPos = this.XPos;
      clonedItem.YPos = this.YPos;            

     if(this.firstPoint != null)
      {
        clonedItem.firstPoint = new Point(this.firstPoint.x,this.firstPoint.y);
      }
      else
        clonedItem.firstPoint = null;
      
      if(this.secondPoint != null)
        clonedItem.secondPoint = new Point(this.secondPoint.x, this.secondPoint.y);
      else
        clonedItem.secondPoint = null;     

      var clonedObject:Image = new Image();
      clonedObject.x = this.obj.x;
      clonedObject.y = this.obj.y;
      clonedObject.source = this.obj.source;         
      
      clonedItem.DrawCurve();         
            
      return (clonedItem as CourtObject);
    }
    
    override public function Save():XML
    {
       var s:XML = <Curve x={XPos} y={YPos} width={this.Width} height={this.Height} drawAtFirst={false}>
                  <HashCode id={this.guid} />
                 </Curve>;
       for each (var point:Point in this.points)
       {
         var sChild:XML =  <Point x={point.x} y={point.y} />;
         s.appendChild(sChild);
      }    
//      var sChildFirstAr:XML =  <Point x={this.ArrowPointStart.x} y={this.ArrowPointStart.y} />;
//         s.appendChild(sChildFirstAr);
//      var sChildSecondAr:XML =  <Point x={this.ArrowPointFinish.x} y={this.ArrowPointFinish.y} />;
//         s.appendChild(sChildSecondAr);         
       return s;
    }
    
    override public function Load(serializedObject:XML):CourtObject
    {
       var so:Curve = new Curve(); 
       so.guid = serializedObject.HashCode.@id.toString();
       so.Height = serializedObject.@height;
       so.Width = serializedObject.@width;
       so.XPos = serializedObject.@x;
       so.YPos = serializedObject.@y;
       so.DrawAtFirst = serializedObject.@drawAtFirst;
       for each(var point:XML in serializedObject.Point)
       {
         so.points.push(new Point(point.@x, point.@y));
       }  
       so.ArrowPointFinish = new Point();
       so.ArrowPointFinish = so.points[so.points.length-1];
       so.ArrowPointStart = new Point();
       so.ArrowPointStart =  so.points[so.points.length-3];  
        
       //Alert.show("Load x="+so.ArrowPointStart.x.toString()+" y="+so.ArrowPointStart.y.toString());
       //Alert.show("Load x="+so.ArrowPointFinish.x.toString()+" y="+so.ArrowPointFinish.y.toString());
       return so as CourtObject;
    }
  }
}