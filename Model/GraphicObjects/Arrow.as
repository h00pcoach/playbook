package Model.GraphicObjects
{
  import Model.CourtObject;
  
  import flash.geom.Point;
  import flash.geom.Rectangle;
  
  import mx.controls.Image;
  import mx.core.Container;
  import mx.utils.UIDUtil;
 
  public class Arrow extends CourtObject
  {
  	
  	
    public var angle:Number;     // угол между основной осью стрелки и рисочки в конце
    public var arrowType:int;    //тип стрелки
    public var dash:int;         // длинна рисоки на стрелке
    
    public var firstPoint:Point;
    public var secondPoint:Point;
	
    override public function get XPos():int 		 { return obj.x; } 
    override public function get YPos():int          { return obj.y; } 
    override public function set XPos(val:int):void  { obj.x = val;  } 
    override public function set YPos(val:int):void  { obj.y = val;  }
    
    override public function get image():Image 
    { 
      return obj; 
    }
	
    public function Arrow(type:int=0):void
    {
      this.firstPoint = null;
      this.secondPoint = null;
      this.arrowType = type;
      this.guid = UIDUtil.createUID();
      
      switch (arrowType)
      {
       case 0:{angle=Math.PI/9; dash=10; break;}
       case 1:{angle=Math.PI/9; dash=10; break;}
       case 2:{angle=Math.PI/9; dash=10; break;}
       case 3:{angle=Math.PI/2; dash=8;  break;}
       case 4:{angle=Math.PI/9; dash=30; break;}
      }  
    }
    
    override public function AddObjectOnFrame(parent:Container):void 
    {      
      obj.x = XPos;
      obj.y = YPos;
      parent.addChild(obj);
    }
    
    public function DrawLineArrow(p1:Point, p2:Point, alfa:Number, r1:Number):void
    {
           //alfa угол между основной осью стрелки и рисочки в конце
           //r1 длинна риски
           //beta это арктангенс от x/y (width/height)
           //obj.graphics.lineStyle(3,0);
           var beta:Number = Math.atan2(Math.abs(p1.y-p2.y),Math.abs(p1.x-p2.x));
           if (((p2.y-p1.y)!=0)||((p2.x-p1.x)!=0))
            {//первая четверть
                  if ((p2.x>=p1.x) && (p2.y<=p1.y))
                  { var xl:Number = p2.x  - obj.x - r1 * Math.cos(beta-alfa);
                    var yl:Number =  p2.y  - obj.y + r1 * Math.sin(beta-alfa);
                    obj.graphics.moveTo(p2.x - obj.x, p2.y - obj.y);  
                    obj.graphics.lineTo(xl, yl);
                    //координаты правой риски 
                    var xr:Number =  p2.x  - obj.x- r1 * Math.cos(beta+alfa);
                    var yr:Number =  p2.y  - obj.y + r1 * Math.sin(beta+alfa);
                    obj.graphics.moveTo(p2.x - obj.x, p2.y - obj.y);
                    obj.graphics.lineTo(xr, yr);}
                  //ворая четверть
                  if ((p2.x<p1.x) && (p2.y<=p1.y))
                  { xl = p2.x  - obj.x + r1 * Math.cos(beta-alfa);
                    yl =  p2.y  - obj.y + r1 * Math.sin(beta-alfa);
                    obj.graphics.moveTo(p2.x - obj.x, p2.y - obj.y);  
                    obj.graphics.lineTo(xl, yl);
                    //координаты правой риски 
                    xr =  p2.x  - obj.x + r1 * Math.cos(beta+alfa);
                    yr =  p2.y  - obj.y + r1 * Math.sin(beta+alfa);
                    obj.graphics.moveTo(p2.x - obj.x, p2.y - obj.y);
                    obj.graphics.lineTo(xr, yr);}        
                  //третья четверть
                  if ((p2.x<p1.x) && (p2.y>p1.y))
                  { xl = p2.x  - obj.x + r1 * Math.cos(beta-alfa);
                    yl =  p2.y  - obj.y - r1 * Math.sin(beta-alfa);
                    obj.graphics.moveTo(p2.x - obj.x, p2.y - obj.y);  
                    obj.graphics.lineTo(xl, yl);
                    //координаты правой риски 
                    xr =  p2.x  - obj.x + r1 * Math.cos(beta+alfa);
                    yr =  p2.y  - obj.y - r1 * Math.sin(beta+alfa);
                    obj.graphics.moveTo(p2.x - obj.x, p2.y - obj.y);
                    obj.graphics.lineTo(xr, yr);}
                  //четвертая четверть
                  if ((p2.x>=p1.x) && (p2.y>=p1.y))
                  { xl = p2.x  - obj.x - r1 * Math.cos(beta-alfa);
                    yl=  p2.y  - obj.y - r1 * Math.sin(beta-alfa);
                    obj.graphics.moveTo(p2.x - obj.x, p2.y - obj.y);  
                    obj.graphics.lineTo(xl, yl);
                    //координаты правой риски 
                    xr =  p2.x  - obj.x - r1 * Math.cos(beta+alfa);
                    yr =  p2.y  - obj.y - r1 * Math.sin(beta+alfa);
                    obj.graphics.moveTo(p2.x - obj.x, p2.y - obj.y);
                    obj.graphics.lineTo(xr, yr);}           
            }       
     //        }      
    }
   
    private function DrawLine(p1:Point, p2:Point, thickness:int):void
    {
     //thickness - толщина рисуемой линии
     // устанавливаем координаты рисунка на канвасе
     //p2 = new Point(mouseCoods.x - MainLayout.x, mouseCoods.y - MainLayout.y);
      if(obj!=null)
      {
        obj.x = p1.x < p2.x ? p1.x : p2.x;
        obj.y = p1.y < p2.y ? p1.y : p2.y;
      }
     
     var rect:Rectangle = GetImageRectSize(p1, p2);    
     // чиситим канвас и рять рисуем. Для того, чтобы было видно как рисуется стрелочка
     // когда пользователь еще не отпустил стрлку мыши.
     obj.graphics.clear();
     obj.graphics.moveTo(p1.x - obj.x, p1.y - obj.y);
     obj.graphics.lineStyle(thickness,0);
     obj.graphics.lineTo(p2.x - obj.x, p2.y - obj.y);    
    }
        
    public function DrawArrow():void
    {
      if(firstPoint == null || secondPoint == null)
      {
      	trace("Must be 2 point to draw any line")
      	return; 
      }
      
      if(obj==null)
      	obj = new Image();
      
      
      switch(arrowType)
      {
	      case ArrowType.Solid: // прямая
           	DrawLine(firstPoint,secondPoint,3);    //рисуем прямую
           	DrawLineArrow(firstPoint,secondPoint,angle,dash);//рисуем стрелочки
           	break;
          case ArrowType.Dashed: // пунктир
      	   	DrawDashedLine();
      	   	DrawLineArrow(firstPoint,secondPoint,angle,dash);   
        	break;        
      	  case ArrowType.Wavy: //
      	      DrawWaveLine();
      	      DrawLineArrow(firstPoint,secondPoint,angle,dash); //рисуем стрелочки
        	break;        
       	  case ArrowType.TLine: 
	        DrawLine(firstPoint,secondPoint,3);               //рисуем прямую
	        DrawLineArrow(firstPoint,secondPoint,angle,dash); //рисуем стрелочки
			break;
       	  case ArrowType.Double: 
       	  	DrawParallelLine(6);
       	  	DrawLineArrow(firstPoint,secondPoint,angle,dash);
        	break;
          default:
          	trace("Uncnown line type. Please check  'DrawArrow' method");
          	return;      
      }
      GetImageRectSize(firstPoint, secondPoint); 
      return;
    }
    
    // Рисует точку  ( необходима для Debug'а)
    private function DrawPoint(p:Point):void
    {
    	trace("{" + p.x +";"+p.y + "}");
    	this.obj.graphics.lineStyle(2.0);
    	this.obj.graphics.drawCircle(p.x,p.y,5);
    }
    
    // Рисование пунктирной линии
    public function DrawDashedLine():void
    {
    	var dashLenght:Number = 5;
    	var lineLenght:Number =	Point.distance(firstPoint,secondPoint);
    	
    	var pointsCount:int = (lineLenght/dashLenght);
    	
    	if(pointsCount < 2)
    	{
    		DrawLine(firstPoint, secondPoint, 3);
    	}
    	else    	
    	{
    	  var start:Point = new Point(firstPoint.x < secondPoint.x ? firstPoint.x : secondPoint.x, 
                                        firstPoint.y < secondPoint.y ? firstPoint.y : secondPoint.y);
        
        var p1:Point =  new Point(firstPoint.x - start.x,firstPoint.y - start.y);
        var p2:Point = new Point(secondPoint.x - start.x,secondPoint.y - start.y);
        
        obj.x = start.x;
        obj.y = start.y;
        	
      	for(var i:int = 0; i < pointsCount; i++)
      	{
      		var t:Point =	Point.interpolate(p1,p2, i/pointsCount)
      		
      		this.obj.graphics.lineStyle(3,0);
      		
      		if(i%2==0)
      			this.obj.graphics.moveTo(t.x,t.y);
      		else
      			this.obj.graphics.lineTo(t.x,t.y);
      	}
      }
  }

    //рисование волнистой линии
   public function DrawWaveLine():void
   {
      var start:Point = new Point(firstPoint.x < secondPoint.x ? firstPoint.x : secondPoint.x, 
                                           firstPoint.y < secondPoint.y ? firstPoint.y : secondPoint.y);

      obj.x = start.x;
      obj.y = start.y;

      var im:Image = this.obj;
      im.graphics.lineStyle(1,0);
     
      var p1:Point =  new Point(firstPoint.x - start.x,firstPoint.y - start.y);
      var p2:Point = new Point(secondPoint.x - start.x,secondPoint.y - start.y);      
      
      var size:Point = new Point (Math.abs(p2.x-p1.x),Math.abs(p2.y-p1.y));
      im.width = size.x;
      im.height = size.y;
      var beta:Number = Math.atan2(p2.y-p1.y,p2.x-p1.x);
      var lenght:Number = Point.distance(p1,p2);
      
      var curveLenght:Number = 2;
      var curvesCount:Number = lenght/curveLenght;
      
      var pArray:Array = new Array();
      var pUpArray:Array = new Array;
      var pDownArray:Array = new Array();
            
      
      for (var i:int = 0; i < curvesCount; i++)
      {
        //var p:Point = im.globalToContent( Point.interpolate(p1,p2,1-(i/curvesCount)));
        var p:Point = ( Point.interpolate(p1,p2,1-(i/curvesCount)));
        var pUp:Point = new Point(5*Math.sin(Math.PI-beta),5*Math.cos(Math.PI-beta)).add(p);
        var pDown:Point = new Point(5*Math.sin(2*Math.PI-beta),5*Math.cos(2*Math.PI-beta)).add(p);
        pArray.push(p);
        pUpArray.push(pUp);
        pDownArray.push(pDown);
      }  
   
      for ( i = 0; i < pArray.length-8; i+=4)
      {
        im.graphics.moveTo(pArray[i].x,pArray[i].y);
        im.graphics.curveTo( pUpArray[i+1].x, pUpArray[i+1].y, pArray[i+2].x,pArray[i+2].y); 
        
        im.graphics.moveTo(pArray[i+2].x,pArray[i+2].y);
        im.graphics.curveTo(pDownArray[i+3].x, pDownArray[i+3].y, pArray[i+4].x,pArray[i+4].y);               
      }  

      im.graphics.lineTo(pArray[pArray.length-1].x,pArray[pArray.length-1].y);
    }
    
    // Рисование двух паралельных линий
    public function DrawParallelLine(lineWidth:Number):void
    {
        var im:Image = new Image();
        im.graphics.lineStyle(3,0);

        var start:Point = new Point(firstPoint.x < secondPoint.x ? firstPoint.x : secondPoint.x, 
                                           firstPoint.y < secondPoint.y ? firstPoint.y : secondPoint.y);
     
        var p1:Point =  new Point(firstPoint.x - start.x,firstPoint.y - start.y);
        var p2:Point = new Point(secondPoint.x - start.x,secondPoint.y - start.y);
        
        im = this.obj;       
        var size:Point = new Point (Math.abs(p2.x-p1.x),Math.abs(p2.y-p1.y));
        
        obj.x = start.x;
        obj.y = start.y;
         
        var beta:Number = Math.atan2(p2.y-p1.y,p2.x-p1.x);
        var lenght:Number = Point.distance(p1,p2);

        p2 = Point.interpolate(p1,p2,(1-((lenght-20)/lenght)));
        
        var distance:Number = lineWidth;
        var pUp:Point = new Point(distance*Math.sin(-1*(Math.PI + beta)),distance*Math.cos(-1*(Math.PI + beta)));
        pUp.x += p1.x;
        pUp.y += p1.y; 
        
        var pDown:Point = new Point(distance*Math.sin(-beta),distance*Math.cos(-beta));
        pDown.x += p1.x;
        pDown.y += p1.y; 

        
      
        var p2Up:Point = new Point(distance*Math.sin(-1*(Math.PI + beta)),distance*Math.cos(-1*(Math.PI + beta)));
        p2Up.x += p2.x;
        p2Up.y += p2.y;
        
        var p2Down:Point = new Point(distance*Math.sin(-beta),distance*Math.cos(-beta));
        p2Down.x += p2.x;
        p2Down.y += p2.y;
        
        
        im.graphics.lineStyle(3,0);
        im.graphics.moveTo(pUp.x,pUp.y);
        im.graphics.lineTo(p2Up.x,p2Up.y);
        
        im.graphics.moveTo(pDown.x,pDown.y);
        im.graphics.lineTo(p2Down.x,p2Down.y);          
    }    
        
    private static function AngleToDegrees(angleInRad:Number):int
    {
    	return (angleInRad*180)/Math.PI;
    }
    
    private function GetImageRectSize(p1:Point, p2:Point):Rectangle
    {
      var rect:Rectangle = new Rectangle();

      obj.width = Math.abs(p1.x-p2.x);
      obj.height = Math.abs(p1.y - p2.y);

      return rect;
    } 

    override public function CloneCourtOject():CourtObject
    {
      var clonedItem:Arrow = new Arrow();
      
      clonedItem.color = this.color;
      clonedItem.guid = this.guid;
      clonedItem.Height = this.Height;
      clonedItem.Width = this.Width;
      clonedItem.XPos = this.XPos;
      clonedItem.YPos = this.YPos;
      clonedItem.arrowType = this.arrowType;

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
      
      clonedItem.DrawArrow();
            
      return (clonedItem as CourtObject);
    }
  
    override public function Save():XML
    {
       var s:XML = <Arrow type={this.arrowType} x={XPos} y={YPos} width={this.Width} height={this.Height}>
                  <HashCode id={this.guid} />
                    <Point x={this.firstPoint.x} y={this.firstPoint.y} />
                    <Point x={this.secondPoint.x} y={this.secondPoint.y} />
                 </Arrow>;
       return s;
    }
    
    override public function Load(serializedObject:XML):CourtObject
    {
       var so:Arrow = new Arrow(serializedObject.@type); 
       so.arrowType = int(serializedObject.@type);
       so.guid = serializedObject.HashCode.@id.toString();
       so.Height = serializedObject.@height;
       so.Width = serializedObject.@width;
       so.XPos = serializedObject.@x;
       so.YPos = serializedObject.@y;
	   //var tmp:String = serializedObject.Point[0].@x.toString();       
       so.firstPoint = new Point(serializedObject.Point[0].@x, serializedObject.Point[0].@y);
       so.secondPoint = new Point(serializedObject.Point[1].@x, serializedObject.Point[1].@y);
       return so as CourtObject;
    }
  }
}
