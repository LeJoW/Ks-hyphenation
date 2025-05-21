<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<rss version="2.0" xmlns:content="http://purl.org/rss/1.0/modules/content/" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:atom="http://www.w3.org/2005/Atom" xmlns:sy="http://purl.org/rss/1.0/modules/syndication/">
	<channel>
		<title>Comments on:</title>
		<atom:link href="https://einsteinpower.edublogs.org/2021/04/14/carl-gauss/feed/" rel="self" type="application/rss+xml" />
		<link>https://einsteinpower.edublogs.org/2021/04/14/carl-gauss/</link>
		<description></description>
		<lastBuildDate>Thu, 15 Apr 2021 13:21:31 +0000</lastBuildDate>
		<sy:updatePeriod>
			hourly </sy:updatePeriod>
		<sy:updateFrequency></sy:updateFrequency>
		<generator>https://edublogs.org?v=5.4.2</generator>
		<?php array_map(function ($comment) { ?>
			<item>
				<?php array_walk($comment, function ($value, $key) {
					if ($key === "content:encoded" || $key === "description" || $key === "dc:creator") {
						echo "<$key><![CDATA[$value]]></$key>\n";
					} else {
						echo "<$key>$value</$key>\n";
					}
				}); ?>
			</item>
		<?php }, $comments); ?>
	</channel>
</rss>