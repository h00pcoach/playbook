package Model
{
  public class StringFormatter
  {
    public static function BuildString(format:String, ... values):String
    {
      if(format != null && values != null)
      {
        for (var i:int =0; i < values.length; i++)
          format =  format.replace( '{' + i.toString() + '}', values[i].toString()); 
      }
     return format;
    }
  }
}