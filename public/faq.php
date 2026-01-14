<?php
  $title = "MHCT FAQ";
  require_once "common-header.php";
?>

  <div class="panel panel-default">
    <div class="panel-heading">
      <h3 class="panel-title">How often is the data updated?</h3>
    </div>
    <div class="panel-body">
      Attraction and Drop rates are aggregated/updated hourly, while other hunt details might be once or twice daily. Keybase backups are updated daily. (We sometimes run the updates manually when we upgrade/fix something)
    </div>
  </div>

  <div class="panel panel-default">
    <div class="panel-heading">
      <h3 class="panel-title">Anything weird going on with the aggregations?</h3>
    </div>
    <div class="panel-body">
      Drop rates ignore all hunts with Gift of the Day Base because it adds normal loot in unexpected places. This means hunt totals for drops and attractions might be very different. This is not applied to other components that generate loot in unexpected places, such as Snowball Charms making Super Snowball Charms since these loots tend not to be of interest for lookup.
    </div>
  </div>
  <div class="panel panel-default">
    <div class="panel-heading">
      <h3 class="panel-title">Who are you/MHCT?</h3>
    </div>
    <div class="panel-body">
      We are just a group of players like you who like to make MH tools. MHCT is the project we created, and it stands for MouseHunt Community Tools. It includes browser extension and this website. We wanted to share what we made with the community as a way to give back and help out. Started with something small to make our lives easier and grew into this open source project. There are lots of people behind it, some contributed inspiration, others code, some donated money, and thousands have contributed data. We all consider them a valuable part of this project. We couldn't have done this without you!
    </div>
  </div>

  <div class="panel panel-default">
    <div class="panel-heading">
      <h3 class="panel-title">Are there plans for mobile app?</h3>
    </div>
    <div class="panel-body">
      Currently none of us are experts in mobile dev. While we see some potential examples out there to make an app, none of us have done it yet. Maybe you can help?
    </div>
  </div>

  <div class="panel panel-default">
    <div class="panel-heading">
      <h3 class="panel-title">How is this paid for?</h3>
    </div>
    <div class="panel-body">
      We accept donations through Patreon, in the past it's just been one of us paying out of pocket. All donations go to offset the cost of the infrastructure (server, domain name, etc). We do not make any money on this, and we are not planning on monetizing it. While we might be losing a little money, this is not a business, this is a community sponsored project. So paid by community members like you.
    </div>
  </div>

  <div class="panel panel-default">
    <div class="panel-heading">
      <h3 class="panel-title">Do you collect personal data or sell any data?</h3>
    </div>
    <div class="panel-body">
      We only collect game data, as we explicitly state in multiple places. We do not sell any data, it is given away for free for all to use as they want. This has enabled lots of people to analyze it and make tools that go way beyond of what we have on this site. Some examples are Tsitu's tools, Aard's discord bot, etc. The whole point is to collect game data to enable others to build awesome tools and gain interesting insights into the game.
    </div>
  </div>

  <div class="panel panel-default">
    <div class="panel-heading">
      <h3 class="panel-title">Are Hitgrab/MouseHunt devs aware of this? is this allowed?</h3>
    </div>
    <div class="panel-body">
      We chat regularly with the awesome Hitgrab devs, and so far we had no complaints. If we do receive any requests from them, we'll be sure to remove and disable anything and everything that they are against. Afterall, we are here to play the game and enjoy/support the community. That said they've been very supportive so far and even implemented some similar features into the game itself!. Special thanks to them and Wiki contributors, Reddit mods, Discord mods, BT, and others.
    </div>
  </div>

  <div class="panel panel-default">
    <div class="panel-heading">
      <h3 class="panel-title">What about HornTracker?</h3>
    </div>
    <div class="panel-body">
      While Nick gave us great inspiration (and some help) with his HornTracker, we wanted more and different things. For example open sourcing all of the code and data to crowdsource not just the collection of data but also the development of tools and insights. (Sadly Nick is busy with other things, but if he ever decides to come back and develop some more, we would love to have him). The first tool Jack started MHCT with was Map Helper, and it was completely different UI than Horntracker with the goal of making it easy to find map mice. Concentrating more on being intuitive and not having to do any math. While Tsitu's tools for example are more precise and way more mathematically advanced. Some others make scripts for in game UI/UX enhancement, and that's awesome too. Bottom line is we welcome all kinds of tools and are not competing with each other. In fact if you get inspired to build on top of what we have, we welcome it.
    </div>
  </div>

  <div class="panel panel-default">
    <div class="panel-heading">
      <h3 class="panel-title">I have a question, idea, suggestion, complaint, bug report. Where can I voice it?</h3>
    </div>
    <div class="panel-body">
      If it's about official MouseHunt game, please submit it through the game in Help -> Contact Us. If it's about tools, you can talk to us on MH Discord channel <a href="https://discord.gg/E4utmBD" target="_blank">#community-tools</a>. There are no stupid questions, we all were new to this game once.
    </div>
  </div>

<?php require_once "common-footer.php"; ?>
