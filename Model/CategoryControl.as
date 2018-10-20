package Model
{
  import mx.containers.Canvas;
  import mx.controls.List;

  public class CategoryControl extends Canvas
  {
    private var _categoryID:int;
    
    public function get CategoryID():int
    {
      return _categoryID;
    }
    
    public function CategoryControl(id:Number=0)
    {
      this._categoryID = id;
      super();
    }
  }
}