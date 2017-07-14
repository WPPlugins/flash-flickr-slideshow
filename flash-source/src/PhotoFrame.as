package
{
	import flash.display.Bitmap;
	import flash.display.Loader;
	import flash.display.Sprite;
	import flash.events.Event;
	import flash.filters.DropShadowFilter;

	public class PhotoFrame extends Sprite
	{
		private var loader: Loader;
		private var _url: String;
		private var _use_frame: Boolean;
		private var _frame_color: uint;
		private var _frame_size: uint = 5;

		public function PhotoFrame( loader: Loader, url: String )
		{
			super();

			_url = url;
			
			addChild( this.loader = loader );
			Bitmap( loader.content ).smoothing = true;

			addEventListener( Event.ADDED_TO_STAGE, onAdded );
		}

		public function get frame_size():uint
		{
			return _frame_size;
		}

		public function set frame_size(value:uint):void
		{
			_frame_size = value;
		}

		public function get use_frame():Boolean
		{
			return _use_frame;
		}

		public function set use_frame(value:Boolean):void
		{
			_use_frame = value;
		}

		public function get frame_color():uint
		{
			return _frame_color;
		}

		public function set frame_color(value:uint):void
		{
			_frame_color = value;
		}

		public function get url():String
		{
			return _url;
		}

		public function set url(value:String):void
		{
			_url = value;
		}

		private function onAdded( event: Event ): void
		{
			if( use_frame )
			{
				graphics.beginFill( frame_color );
				graphics.drawRect( 0, 0, loader.width + (_frame_size*2), loader.height + (_frame_size*2) );
				graphics.endFill();
				loader.x = _frame_size;
				loader.y = _frame_size;
			}
			loader.mouseEnabled = false;
		}
	}
}