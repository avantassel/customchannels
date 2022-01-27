# Custom Channels Skill Assessment

## Problem Solving

For this section, there are no right or wrong answers. Be as thorough or brief as you’d like and feel free to make any assumptions about existing software/hardware abilities. If making assumptions, explain what those assumptions are and how they impact your answer. These scenarios are examples of the problems this position will regularly be handling.

A user wants a playlist to have 35% Pop Music, 20% Rock Music, and 45% R&B. Each genre of music has thousands of songs associated with it. Describe how you would deliver a dynamically generated playlist that plays indefinitely and:

  - Minimizes repetition
  - Matches the user’s desired percentages
  - Does NOT play four songs by a particular artist in a 3 hour period

> This would be a queuing query that could be called as often as needed and would replace the queue each time.  So if the user wanted to skip or reached the end it could continuously play.  Assuming lastplayed is updated when the song is played.  The `lastplayed` field logic is used to only get songs that have not been played in the last 3 hours.  The `GROUP BY` would assure that there is only 1 song per artist so never reaching 4 songs played in a row by one artist.  The `LIMIT` assures that the user percentages are returned.  The `ORDER BY` is simply to randomize the list to minimize repetition.

```sql
# SQL query to get 100 song playlist
SELECT * FROM
(SELECT artist, song WHERE genre = 'Pop' AND lastplayed < DATE_SUB(NOW(), INTERVAL 3 HOUR) GROUP BY artist LIMIT 35)
UNION
(SELECT artist, song WHERE genre = 'Rock' AND lastplayed < DATE_SUB(NOW(), INTERVAL 3 HOUR) GROUP BY artist LIMIT 20)
UNION
(SELECT artist, song WHERE genre = 'R&B' AND lastplayed < DATE_SUB(NOW(), INTERVAL 3 HOUR) GROUP BY artist LIMIT 45)
ORDER BY RAND()
```

A playlist API writes metadata to a database when a song is played. However, hardware used to listen to the playlist has a built-in buffer which means the audio is delayed by 60-120 seconds from when the metadata is written to the database. How would you display the currently playing 
metadata on a web application so that it matches what a user hears as closely as possible.

> Given the above query I would add an `INSERT` statement at the end of that query to store the queue with a started datetime stamp (Adding 90 seconds to split the difference in buffer time).  Knowing the start datetime, the song length, and the order of the songs in the queue we could know what is playing.

Imagine a user is building a playlist song-by-song but the playlist can’t play 4 songs by the same artist in a 3 hour period. How would you handle this limitation? What parameters would you put in place for the user to follow?

> I would have an artist counter starting at 3, then next to the artist name or add button display the counter if counter is < 3 (ie. 2 left).

How would you approach a migration from a one-to-one user permission structure to a multi-level user permission structure? For example—going from objects having single-user
ownership to allowing multiple users to manage the same object based on admin parameters.

> This is pretty common when adding group / team functionality to an application that had only been single user based.  You would need a relational table to store users that belong to a group.  In the following table, user 1 and 2 are admins and user 3 is not.  Instead of groupId it could be the ID of the object being managed.

| userId | groupId | admin |
|----|----|---|
| 1 | 1 | 1 |
| 2 | 1 | 1 |
| 3 | 1 | 0 |

## Application Project
For the final portion of this assessment, we’d like to ask candidates to create a single-page
application that plays music and displays metadata. Candidates may use any framework they’d
like and are encouraged to be creative with the display/design of the application. The application
should meet the following requirements:

- Ability to play/pause music
- Displayed metadata of currently playing music
- Hosted on public (or private) GitHub repo for review

**Music URL:**
https://stream.customchannels.net/dev_test_96.mp3

**Currently Playing Metadata:**
https://lambda.customchannels.rocks/nowplaying?url=http://stream.customchannels.net/dev_test_96.mp3

**Recently Played Metadata:**
https://ensemble.customchannels.net/api/channels/222/recent

> Application details below.

1. Simple PHP app

    ```sh
    cd src
    php -S 127.0.0.1:8089
    ```
    
![DarkMode Screenshot](screenshot.png)