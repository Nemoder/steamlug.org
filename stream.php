<?php
	$pageTitle = "Live Stream";
	include_once('includes/header.php');
    include_once('includes/MurmurQuery.php');
	require_once('rbt_prs.php');
	require_once('steameventparser.php');

	header("Cache-Control: public, max-age=10");

	$settings               =       array
	(
			'host'          =>      '130.226.217.215',
			'port'          =>      27800,
			'timeout'       =>      200,
			'format'        =>      'json'
	);
	$murmur = new MurmurQuery();
	$murmur->setup($settings);
	$murmur->query();
	$status = $murmur->get_status();
	$info = $status['original'];
	//$channels = $murmur->get_channels();
	$mumbleHeader = ": Offline";
	if($murmur->is_online()) {
		$mumbleHeader = ': Online, ' . count($murmur->get_users()) . ' ⁄ ' . $info['x_gtmurmur_max_users'];
	}

	$parser = new SteamEventParser();

	$month = gmstrftime("%m")-0; // Yuck, apparently the 0 breaks something?
	$year = gmstrftime("%Y");
	$data = $parser->genData("steamlug", $month, $year);

	$gotCurl = false;
	$someoneStreaming = false;
	$twitchOnline = false;
	$hitboxOnline = false;

if ( extension_loaded('curl') ) {
	$gotCurl = true;
	/*
	http://api.hitbox.tv/team/steamlug
	http://api.hitbox.tv/user/johndrinkwater
	https://api.twitch.tv/kraken/streams?channel=steamlug
	{"streams":[],"_to tal":0,"_links":{"self":"https://api.twitch.tv/kraken/streams?channel=steamlug&limit=25&offset=0","next":"https://api.twitch.tv/kraken/streams?channel=steamlug&limit=25&offset=25","featured":"https://api.twitch.tv/kraken/streams/featured","summary":"https://api.twitch.tv/kraken/streams/summary","followed":"https://api.twitch.tv/kraken/streams/followed"}}
	*/

	/* This should return a JSON string, or an error! */
	function curl_url( $url, $get = array(), $header = array() ) {

		$curl = curl_init( );
		curl_setopt_array( $curl, array(
				CURLOPT_URL => $url . '?' . http_build_query( $get ),
				CURLOPT_HTTPHEADER => $header,
				CURLOPT_HEADER => 0,
				CURLOPT_RETURNTRANSFER => 1,
				CURLOPT_CONNECTTIMEOUT => 1,
				CURLOPT_TIMEOUT => 1 )
				);
		$result = curl_exec( $curl );
		$status = curl_getinfo( $curl, CURLINFO_HTTP_CODE );

		if ( ( $status == 404 ) || ( $status == 0 ) || ( $status == 503 ) ) {
			return curl_error( $curl ) . ", " .curl_errno( $curl );
		}
		curl_close( $curl );
		return $result;
	}

	$streamers = '<div class="row">';
/* Twitch */
	$maybeOnline = curl_url( 'https://api.twitch.tv/kraken/streams/steamlug', array(), array( 'Accept: application/vnd.twitchtv.v3.json' ) );
	$twitchStream = json_decode( $maybeOnline, true );
	if ( $twitchStream['stream'] != null ) {
		print "online";
		$someoneStreaming = true;
		$twitchOnline = true;
	}

/* HitBox */
	$hitboxUsers = curl_url( 'http://api.hitbox.tv/team/steamlug' );
	$hitboxStreamers = @json_decode( $hitboxUsers, true );
	$hitboxPeeps = "";
	if ( $hitboxStreamers != null ) {
		foreach ( $hitboxStreamers['members'] as $streamer ) {
			$hitboxPeeps .= '<li class="' . ($streamer['is_live'] == 1 ? 'live': '' ) .  '">';
			$hitboxPeeps .= '<a href="//hitbox.tv/' . $streamer['user_name'] . '">';
			$hitboxPeeps .= '<img src="//edge.sf.hitbox.tv/' . $streamer['user_logo_small'] . '" />';
			$hitboxPeeps .= $streamer['user_name'] . '</a>';
			$hitboxPeeps .= '</li>';
			if ( $streamer['user_name'] == 'steamlug' and $streamer['is_live'] == 1) {
				$someoneStreaming = true;
				$hitboxOnline = true;
			}
		}
	$streamers .= <<<HITBOXBOX
						<div class="col-sm-12">
							<article class="panel panel-default">
								<header class="panel-heading">
									<h3 class="panel-title">{$hitboxStreamers['info']['group_display_name']} hitbox team</h3>
								</header>
								<div class="panel-body">
									<p>{$hitboxStreamers['info']['group_text']}</p>
									<ul id="hitbox-list">
										{$hitboxPeeps}
									</ul>
								</div>
							</article>
						</div>
HITBOXBOX;
	}
	$streamers .= '</div>';
}


/* If Streaming, we should: hide <h1> title, remove Next Event: */
/* If not-streaming, show current hitbox team roster? */
if (!$someoneStreaming) {
	print '<h1 class="text-center">Live Stream</h1>';
}
?>
					<div class="row no-content">
						<div class="col-sm-6">
							<article class="panel panel-default">
								<header class="panel-heading">
									<h3 class="panel-title"><a href="/mumble">Mumble<?=$mumbleHeader; ?></a></h3>
								</header>
							</article>
						</div>
						<div class="col-sm-6">
							<article class="panel panel-default">
								<header class="panel-heading">
									<h3 class="panel-title"><a href="<?=$data["events"][0]["url"];?>"><?=$data["events"][0]["title"];?></a></h3>
								</header>
<!--							<div class="panel-body">
									<p class="text-center"><img src="<?=$data["events"][0]["img_header"];?>" alt="<?=$data["events"][0]["title"];?>" /></p>
									<p><a href="<?=$data["events"][0]["url"];?>" class="btn btn-primary">Click for details</a></p>
								</div>-->
							</article>
						</div>
					</div>
<?php

if ($hitboxOnline) {

echo <<<HITBOX
			<article class="panel panel-default">
				<header class="panel-heading">
					<h3 class="panel-title"><a href="https://hitbox.tv/steamlug">SteamLUG on HitBox</a></h3>
				</header>
				<div class="panel-body">
					<div id="hitbox-viewer"><iframe width="640" height="360" src="//hitbox.tv/#!/embed/steamlug?autoplay=true" frameborder="0" allowfullscreen></iframe></div>
					<p><a href="http://www.hitbox.tv/steamlug" class="btn btn-primary">Click for larger stream</a></p>
				</div>
			</article>
HITBOX;
} else if ($twitchOnline) {

echo <<<TWITCH
			<article class="panel panel-default">
				<header class="panel-heading">
					<h3 class="panel-title"><a href="https://twitch.tv/steamlug">SteamLUG on Twitch</a></h3>
				</header>
				<div class="panel-body">
					<object type="application/x-shockwave-flash" width="100%" height="600px" id="live_embed_player_flash" data="http://www.twitch.tv/widgets/live_embed_player.swf?channel=steamlug">
						<param name="allowFullScreen" value="true" />
						<param name="allowScriptAccess" value="always" />
						<param name="allowNetworking" value="all" />
						<param name="movie" value="http://www.twitch.tv/widgets/live_embed_player.swf" />
						<param name="flashvars" value="hostname=www.twitch.tv&channel=steamlug&auto_play=true&start_volume=25" />
					</object>
					<p><a href="http://www.twitch.tv/steamlug" class="btn btn-primary">Click for larger stream</a></p>
				</div>
			</article>
TWITCH;
}
if ($someoneStreaming == false or $gotCurl == false ) {
	print <<<WHELP
			<div class="panel panel-default">
				<header class="panel-heading">
					<h3 class="panel-title">Streams offline</h3>
				</header>
				<div class="panel-body">
					<p>It looks like no one from the community is streaming right now.</p>
					<!-- put some links here to main channels? -->
				</div>
			</div>
WHELP;
}
if ( $someoneStreaming == false ) {

	print $streamers;
}

include_once('includes/footer.php');
