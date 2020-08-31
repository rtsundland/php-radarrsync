# php-radarrsync
RadarrSync written in PHP and leverages Radarr Lists

So uh I don't like python, but I found the traditional python-based RadarrSync a little hacky.

After playing around with the Radarr API and looking at the List options, I found a better option.  Ultimately, this script simply acts as a "proxy" between your two Radarr instances, and allows you to perform some _basic_ filtering of the items from your source Radarr instance.

To use this code, you're going to need something like Nginx or Apache that can run PHP code.  For me, I use Nginx to proxy several services, so adding a script into my root directory wasn't difficult, but YMMV.

On your >destination< Radarr list, you will create a new list (Settings > Lists).  Add a new List of type "Radarr List" and fill it out as follows:

  * Name:  whatever you want
  * Enable Automatic Sync: Yes  (Set to "No" initially, change later)
  * Add Movies Monitored: Yes  (Set to "No" initially, change later)
  * Minimum Availability: Announced   (let your source instance manage/refine availability)
  * Quality Profile: The profile you want to use
  * Folder: Destination Folder for your movies
  * Tags: <blank>
  * Radarr API URL:  [see below]
  * Path to List: <blank>
  
Radarr API URL would be the URL of your radarrsync.php installation with some added HTTP GET parameters.

  For example, your radarrsync.php is accessible at http://plex/radarrsync.php
  
  You _must_ pass the following GET parameters:
  
   * source=address:port[/urlbase]
    
      source defines the address, port, and URL base of the source Radarr instance
          - address is the hostname or IP of the remote instance
          - port and urlbase (if applicable) is the Port Number and URL Base
            of the remote instance (Settings > General)
        
   * apikey=<string>
    
      API key needed to access the source Radarr Instance (Settings > General)
    
   * ssl=[0|1]
      
      OPTIONAL: If the source instance requires the use of SSL, default is 0
    
  Beyond these 3 variables, all other variables passed to the URL of this script will be
  interpretted as filters.  For instance, you can filter on the source list profileId
  by adding e.g. profileId=5 to the URL.

  Using profileId would be equivalent to support what the "python" version of
  RadarrSync does, but you can optionally create filters on other items, too.
  
  As an example, we're going to use the following values:
    * source = radarr.local:7878
    * apikey = abc1234
    * ssl = 0
    * profileId = 5
  
  Using these values, your Radarr API url would look something like:  http://plex/radarrsync.php?source=radarr.local:7878&apikey=abc1234&ssl=0&profileId=5
  
Save your entry and go to 'Add Movies' and choose 'Add Movies from List'.  In the dropdown, choose the list you just created and click 'Fetch List.'

The movie list should appear.  A nice feature of Radarr is that it will only identify movies that have not been sync'd, so your list may be empty.  Try adding a new movie to your source Radarr instance, putting it in the correct profile, and try it again.

Automating it!

This is why I like this method better:

  * Modify the List you created (Settings > Lists).
      - Enable Automatic Sync = Yes
      - Add Movies Monitored = Yes
      - Save
  
  * On Settings > Lists, under Options:
      - List Update Interval = <number of minutes>
      - Clean Library Level = <pick an option>
      - Save
  
      NOTE:  This options you'll want to adjust according to your desired outcome.  For instance, if you want to delete the movie from the second instance when it's deleted from the first instance, you can choose 'Remove and Delete Files' under Clean Library Level.
      
That's it!


