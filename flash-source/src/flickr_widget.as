package
{
	import caurina.transitions.Tweener;
	
	import com.adobe.fileformats.vcard.Phone;
	import com.adobe.webapis.flickr.FlickrService;
	import com.adobe.webapis.flickr.Photo;
	import com.adobe.webapis.flickr.events.FlickrResultEvent;
	
	import flash.display.DisplayObject;
	import flash.display.Loader;
	import flash.display.LoaderInfo;
	import flash.display.Sprite;
	import flash.display.StageAlign;
	import flash.display.StageScaleMode;
	import flash.events.ErrorEvent;
	import flash.events.Event;
	import flash.events.IOErrorEvent;
	import flash.events.MouseEvent;
	import flash.filters.DropShadowFilter;
	import flash.geom.Rectangle;
	import flash.net.URLRequest;
	import flash.net.navigateToURL;
	import flash.system.ApplicationDomain;
	import flash.system.LoaderContext;
	import flash.utils.setTimeout;

	/**
	 * @author Alessandro Crugnola
	 * @version 0.3
	 */
	
	[SWF( frameRate="60" )]
	public class flickr_widget extends Sprite
	{
		private var api_key: String = "ac2255e9ab52edad6a5cfbca20b71486";

		private var container: Sprite;
		private var current: Photo;
		private var frame_color: uint = 0xFFFFFF;
		private var photo_count: uint = 0;
		private var photo_type: String = "b";
		private var service: FlickrService;
		private var timer: uint = 3000;
		private var use_frame: Boolean;
		private var use_shadow: Boolean;
		private var user_id: String;
		private var username: String = "";
		private var frame_size: uint = 5;

		public function flickr_widget()
		{
			container = new Sprite();
			addChild( container );
			addEventListener( Event.ADDED_TO_STAGE, onAdded );
		}

		private function delayLoadNextPhoto(): void
		{
			setTimeout( getNextPhoto, timer );
		}

		private function displayPhoto( loader: Loader ): void
		{
			loader.contentLoaderInfo.removeEventListener( Event.COMPLETE, onLoadComplete );

			var frame: PhotoFrame = new PhotoFrame( loader, getPhotoLink( current ) );
			if ( use_shadow )
				frame.filters = [ new DropShadowFilter( 4, 45, 0, 1, 4, 4, 1, 3 ) ];

			frame.frame_color = frame_color;
			frame.use_frame = use_frame;
			frame.frame_size = frame_size;
			frame.useHandCursor = true;
			frame.buttonMode = true;
			frame.addEventListener( MouseEvent.CLICK, onPhotoClick );

			container.addChild( frame );

			var rect: Rectangle = frame.getRect( frame );

			var w: int = frame.width;
			var h: int = frame.height;

			var ratio_w: Number = w / ( stage.stageWidth - 10 );
			var ratio_h: Number = h / ( stage.stageHeight - 10 );

			if ( ratio_w > 1 || ratio_h > 1 )
			{
				var ratio: Number = Math.max( ratio_w, ratio_h );
				w = w / ratio;
				h = h / ratio;

				trace( w, h, stage.stageWidth, stage.stageHeight );
			}

			frame.width = w;
			frame.height = h;


			frame.x = ( stage.stageWidth - frame.width ) / 2;
			frame.y = ( stage.stageHeight - frame.height ) / 2;

			frame.alpha = 0;
			Tweener.addTween( frame, { alpha: 1, time: 1, transition: "linear", onComplete: delayLoadNextPhoto } );

			if ( container.numChildren > 1 )
			{
				var previous_loader: DisplayObject = container.getChildAt( container.numChildren - 2 );
				Tweener.addTween( previous_loader, { alpha: 0, time: 1, transition: "linear", onComplete: removePreviousPhoto, onCompleteParams: [ previous_loader ] } );
			}
		}

		private function getNextPhoto(): void
		{
			if ( photo_count > 0 && user_id != null )
			{
				service.people.getPublicPhotos( user_id, "", 1, Math.random() * photo_count );
			}
		}

		private function getPhotoLink( photo: Photo ): String
		{
			return "http://www.flickr.com/photos/" + user_id + "/" + photo.id;
		}

		/**
		 *
		 * @param type mstb
		 */
		private function getPhotoUrl( photo: Photo, type: String = "s" ): String
		{
			return "http://farm" + photo.farmId + ".static.flickr.com/" + photo.server + "/" + photo.id + "_" + photo.secret + ( type != "-" ? "_" + type :
					"" ) + ".jpg";
		}

		private function loadNextPhoto( photo: Photo ): void
		{
			current = photo;
			var loader: Loader = new Loader();
			var context: LoaderContext = new LoaderContext( true );
			loader.contentLoaderInfo.addEventListener( Event.COMPLETE, onLoadComplete );
			loader.contentLoaderInfo.addEventListener( IOErrorEvent.IO_ERROR, onIOError );
			
			try
			{
				loader.load( new URLRequest( getPhotoUrl( photo, photo_type ) ), context );
			} catch( error: Error )
			{
				trace( error );
			}
		}
		
		private function onIOError( event: IOErrorEvent ): void
		{
			trace('io error', event.text );
			delayLoadNextPhoto();
		}

		private function onAdded( event: Event ): void
		{
			stage.scaleMode = StageScaleMode.NO_SCALE;
			stage.align = StageAlign.TOP_LEFT;


			var paramObj: Object = LoaderInfo( this.root.loaderInfo ).parameters;
			if ( paramObj.hasOwnProperty( "username" ) )
				username = paramObj.username;
			else
				username = "acrugnola";

			if ( paramObj.hasOwnProperty( "timer" ) )
				timer = parseInt( paramObj.timer );

			if ( paramObj.hasOwnProperty( "api_key" ) )
				api_key = paramObj.api_key;

			if ( paramObj.hasOwnProperty( "image_type" ) )
				photo_type = paramObj.image_type;

			if ( paramObj.hasOwnProperty( "use_frame" ) )
				use_frame = paramObj.use_frame == "1";

			if ( paramObj.hasOwnProperty( "use_shadow" ) )
				use_shadow = paramObj.use_shadow == "1";

			if ( paramObj.hasOwnProperty( "frame_color" ) )
				frame_color = parseInt( paramObj.frame_color );
			
			if ( paramObj.hasOwnProperty( "frame_size" ) )
				frame_size = parseInt( paramObj.frame_size );

			trace( "timer", timer );
			trace( "use_frame", use_frame );
			trace( "use_shadow", use_shadow );
			trace( "frame_color", frame_color.toString( 16 ) );
			trace( "image_type", photo_type );
			trace( "frame_size", frame_size );

			service = new FlickrService( api_key );
			service.addEventListener( FlickrResultEvent.PEOPLE_FIND_BY_USERNAME, onPeopleFindByUserName );
			service.addEventListener( FlickrResultEvent.PEOPLE_GET_INFO, onPeopleGetInfo );
			service.addEventListener( FlickrResultEvent.PEOPLE_GET_PUBLIC_PHOTOS, onPeopleGetPublicPhotos );
			service.addEventListener( IOErrorEvent.IO_ERROR, onServiceError );

			service.people.findByUsername( username );
		}
		
		private function onServiceError( event: IOErrorEvent ): void
		{
			trace('error', event.text );
		}

		private function onLoadComplete( event: Event ): void
		{
			displayPhoto( LoaderInfo( event.target ).loader );
		}

		private function onPeopleFindByUserName( event: FlickrResultEvent ): void
		{
			try
			{
				if ( event.success )
				{
					user_id = event.data.user.nsid;
					service.people.getInfo( user_id );
				} else
				{
					trace( "onPeopleFindByUserName, success = false" );
				}
			} catch ( error: Error )
			{
				trace( error );
			}
		}

		private function onPeopleGetInfo( event: FlickrResultEvent ): void
		{
			try
			{
				if ( event.success )
				{
					photo_count = event.data.user.photoCount;
					getNextPhoto();
				}
			} catch ( error: Error )
			{
				trace( error );
			}
		}

		private function onPeopleGetPublicPhotos( event: FlickrResultEvent ): void
		{
			try
			{
				if ( event.success )
				{
					if ( event.data.photos.photos && event.data.photos.photos.length > 0 )
					{
						var photo: Photo = event.data.photos.photos[0] as Photo;
						loadNextPhoto( photo );
					} else
					{
						trace( "onPeopleGetPublicPhotos, photos = 0" );
					}
				} else
				{
					trace( "onPeopleGetPublicPhotos, success = false" );
				}
			} catch ( error: Error )
			{
				trace( error );
			}
		}

		private function onPhotoClick( event: MouseEvent ): void
		{
			var frame: PhotoFrame = event.currentTarget as PhotoFrame;
			navigateToURL( new URLRequest( frame.url ), "_blank" );
		}

		private function removePreviousPhoto( loader: DisplayObject ): void
		{
			container.removeChild( loader );
		}
	}
}