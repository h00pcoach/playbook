package Model
{
	import Model.GraphicObjects.Arrow;
	import Model.GraphicObjects.Ball;
	import Model.GraphicObjects.Curve;
	import Model.GraphicObjects.Forward;
	import Model.GraphicObjects.Player;
	
	import mx.controls.Image;
	
  
  // Основной класс, описывающий общую схему игры. 
  // содержит список кадров, которые содержат позиции объектов. 
  public class PlayScheme 
  {
    // заголовок для текущей схемы 
    public var title:String = "";
    
    public var currentMoutionSpeed:int = 2;
    
    public var description:String = "";
    
    public var SaveName:String = "";
    
    public var Rating:int = 0;
    


    // униальный номер схемы (id в базе данных). По нему, например,
    // можно производить загрузку информации 
    public var id:int = -1;
    
    // размеры прощадки
    public var courtWidth:int = 0;
    public var courtHeigth:int = 0;
	
	  [Embed(source="Model/Img/Frame.jpg")]
	  [Bindable]
	  private var icocategor01ImageClass:Class;
    	  	
	  [Embed(source="Model/Img/Frame01.png")]
	  [Bindable]
	  private var icocategor02ImageClass:Class;
	  

    public var courtBackgroundImageUrl:Class;
    public var courtBackgroundImageType:int = 1;
    
    public var courtImgArray:Array = new Array();
    
    //установка цвета корта
    public function SetCourtColor(imageIndex:int):void
    {
      switch(imageIndex)
      {
        case(1): courtBackgroundImageUrl=icocategor01ImageClass;
        		 courtBackgroundImageType = 1;
                 break;
        case(2): courtBackgroundImageUrl = icocategor02ImageClass;
        		 courtBackgroundImageType = 2;
                 break;
      }
    }
    
  	public function IsImageCourtObject(img:Image):Boolean
	  {
	    for each(var imgClass:Class in courtImgArray)
	    {
	      if(img.source == imgClass)
	        return true
	    }
      return false;
    }

    
    // Массив последовательностей (ключевых кадров). 
    // В программе PlayBook (http://www.jes-basketball.com/playbook) это 
    // называется Sequence. Каждый кадр - описание текущих позиций всех 
    // объектов на площадке. 
    public var frameArray:Array = new Array();
    
    public function PlayScheme():void
    {
      courtImgArray.push(icocategor01ImageClass);
      courtImgArray.push(icocategor02ImageClass);
    }
    
    // указыает на то, какой фрейм активен и какой нужно проигрывать.
    // функции PlayNextFrame & PlayNextFrame должны использовать это значение.
    // Для изенения или получения значения надо 
    // пользоваться GetCurrentFrameIndex & SetCurrentFrameIndex
    private var currentFrameIndex:int = -1;
    
    // Возвращает currentFrameIndex, внимательно проверяя, не вышли ли мы заграницы 
    // массива кадров.
    public function GetCurrentFrameIndex():int
    {
      if(frameArray.length == 0)
        return -1;
        
      if( (frameArray.length -1) >= currentFrameIndex)
        return currentFrameIndex
      else
        return (frameArray.length -1);
    }
    
    // Устанавливает currentFrameIndex, внимательно проверяя, не вышли ли мы заграницы 
    // массива кадров.
    // Если значение было успешно установлено, возвращает true, иначе false.
    public function SetCurrentFrameIndex(i:int):Boolean
    {
      if( (frameArray.length -1) >= i)
      {
        currentFrameIndex = i;
        return true;
      } 
      else
        return false;
    }
    
    
    // проигрывает анимацию для следующего фрейма
    public function PlayNextFrame():void
    {
      // тут надо проиграть анимацию следующего кадра
      // перескакиваем на след. кадр
      SetCurrentFrameIndex(GetCurrentFrameIndex() + 1);
    }

    // проигрывает анимацию для предыдущего фрейма
    public function PlayPrevFrame():void
    {
      // тут надо проиграть анимацию предыдущего кадра 
      // перескакиваем на след. кадр
      SetCurrentFrameIndex(GetCurrentFrameIndex() - 1);
    }
    
    // проигрывает все фреймы по очереди. Полная анимация
    public function PlayAllFrame():void
    {
      // тут надо проиграть анимацию предыдущего кадра
      // скорее всего тут надо установить указатель на последний фрейм. 
      // SetCurrentFrameIndex( ???? );
    }   
    
    //создание копии PlayScheme
    public function CloneObject():PlayScheme
    {
      var res:PlayScheme = new PlayScheme(); 
    
      for each(var f:Frame in frameArray)
      {
        var newFrame:Frame = new Frame();
        newFrame.name = f.name;
      
        for each(var co:CourtObject in f.courtObjectArray)
        {
          if(!(co is Arrow)&& !(co is Curve))
          {
            var newCourtObj:CourtObject = co.CloneCourtOject();
            newFrame.courtObjectArray.push(newCourtObj);
          }
        }
        res.frameArray.push(newFrame);
      } 
      return res;
    }
    
    // Загружает объект из строки, принятой от вэбсервиса
    // TODO: доработать. повставлять проверку ошибок и перехват исключений
    // то же самое сделать  в классах Player, Ball, Forward
    // добавить загрузку стрелок когда будут готовы 
     public static function Load(dataStr:String):PlayScheme
  	 {
  	   var data:XML = new XML(dataStr);
	     var tempSheme:PlayScheme = new PlayScheme();
  	   tempSheme.id = data.@id;
  	   tempSheme.title = data.@name;
  	   tempSheme.courtHeigth = data.@courtHeigth;
  	   tempSheme.courtWidth=data.@courtWidth;
  	   tempSheme.courtBackgroundImageType = data.@courtBkgType;
  	   tempSheme.SaveName = data.@saveName;
  	   tempSheme.currentMoutionSpeed = data.@speed;
    	 tempSheme.Rating = data.@rating;		    
       // 4 foreach надо для того чтобы вызывать 
       // метод Load для каждого объекта свой
       // TODO: Если будеит время позаменять на switch
  	   for each (var _frame:XML in data.Frame)
       {
          var frame:Frame = new Frame();
          frame.name = _frame.@name;
          frame.comments = _frame.@comments;
      	  var c:CourtObject;

	      for each (var node:XML in _frame.Ball)
        {
          c = new Ball().Load(node);
          frame.courtObjectArray.push(c);
        }
        for each (node in _frame.Player)
        {
    	    c = new Player().Load(node);
    	    frame.courtObjectArray.push(c);
        }
        for each (node in _frame.Forward)
        {
    	    c = new Forward().Load(node);
    	    frame.courtObjectArray.push(c);
        }
        for each (node in _frame.Arrow)
        {
    	    c = new Arrow().Load(node);
    	    frame.courtObjectArray.push(c);
        }
        for each (node in _frame.Curve)
        {
          c = new Curve().Load(node);
          frame.courtObjectArray.push(c);
        }
        tempSheme.frameArray.push(frame);
       }
       return tempSheme;
     }
  }
}