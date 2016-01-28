# bandcampscraper_php

## Synopsis

A small PHP utility that takes a url for a Bandcamp album or track page and returns a JSON object with all relevant metadata on that page. Original purpose of this was to be able to add music from and link back out to a Bandcamp page from a custom player (e.g. the FullwidthAudioPlayer by radykal), much like you can via the SoundCloud API.

## Code Example

'''JQuery
var bandcampUrl = "[...]/bandcampScraper.php?url=https://abdominal.bandcamp.com/track/broken";
$.getJSON( bandcampUrl, function( bandcampData ) {
  var artist = bandcampData.artist;
  var numTracks = bandcampData.trackInfo.length;
  //for more, please see the API breakdown
}).fail(function(jqXHR) {
  if (jqXHR.status == 404) {
    alert("The url passed could not be found");
  } else {
    alert("The url resolves but does not contain any track data. ");
  }
});
'''

Returns a JSON object with the track's or album's metadata, or some kind of error-like response, if the scraped data isn't in the expected format. The object is simply a JSON representation of the JS object "var TralbumData", which is present on all these pages.

## Motivation

When working with SoundCloud, they make it really easy by providing an API for you to access the data, for use in custom media players and the like. Bandcamp used to also do this, but for whatever reason, have decided revoked access to their API. This is a very crude way of re-adding some sort of API access.

PLEASE NOTE: This is designed to help intergrate, share and LINK BACK to the artist's music, not just to bootleg it. Before using this API, please read the following:
 http://bandcamp.com/help/audio_basics#steal
 http://bandcamp.com/terms_of_use

I am not responsible for any misuse of the data - support your artists and buy their music. Or at least use this api to tell people about them.

## Installation

Drop the file anywhere on the same server as the JS file that will be making the get call and simply perform a get call from your javascript.

## API Reference

TODO.

## Contributors

Project kicked off by Nikolay Timofeev (bachelarius) and all are welcome to join in and make it better - I am no PHP dev, so I have almost certainly used bad practices.

## License

MIT License - Feel free to use it and change it but please provide credit back to this git page.
