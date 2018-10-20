package Model
{
  import Model.GraphicObjects.Arrow;
  import Model.GraphicObjects.Curve;
  
  // Описание текущих позиций объектов на корте. 
  public class Frame
  {
    // список всех объектов, которые находятся на этом фрейме
    public var courtObjectArray:Array = new Array();
    
    //комментарии к фрейму
    public var comments:String = "";
    
    public function RemoveObj(obj:CourtObject):void
    {
      for(var i:int=0;i<courtObjectArray.length;i++)
      {
        if((obj is Arrow)||(obj is Curve))
        {
          if(obj.image == (courtObjectArray[i] as CourtObject).image)
          {
            courtObjectArray.splice(i,1);
            break;
          }
        }
        else
        {
          if(obj.image.source == (courtObjectArray[i] as CourtObject).image.source)
          {
            courtObjectArray.splice(i,1);
            break;
          }
        }
      }
    }
     
    public var name:String = "";
    
    public function Frame()
    {
    }
    
  }
}