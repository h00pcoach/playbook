package Model
{
  public class StringValidator
  {
    public function StringValidator()
    {
    }
    
    public static function check(s:String):String
    {
      var myPattern01:RegExp = new RegExp("'","g");  
      var myPattern02:RegExp = new RegExp("\"","g");  
      var myPattern03:RegExp = new RegExp("<","g");  
      var myPattern04:RegExp = new RegExp(">","g");  

      return s.replace(myPattern01, "").replace(myPattern02,"").replace(myPattern03, "(").replace(myPattern04, ")");
    }

  }
}