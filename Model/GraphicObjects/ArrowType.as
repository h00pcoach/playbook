package Model.GraphicObjects
{
	public final class ArrowType
	{
		public static const Solid:int  = 0;
		public static const Dashed:int = 1;
		public static const Wavy:int   = 2;
		public static const TLine:int  = 3;
		public static const Double:int = 4;
		
		private var _type:int = 0; // default is solid line
		public function get Type():int
		{
			return this.Type;
		}
		
		public function set Type(value:int):void
		{
		  _type = value;
		}

	}
}