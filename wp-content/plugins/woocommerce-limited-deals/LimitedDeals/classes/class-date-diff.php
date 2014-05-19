<?php

/**
 * Calculate differences between two dates with precise semantics. Based on PHPs DateTime::diff()
 * implementation by Derick Rethans. Ported to PHP by Emil H, 2011-05-02. No rights reserved.
 *
 * See here for original code:
 * http://svn.php.net/viewvc/php/php-src/trunk/ext/date/lib/tm2unixtime.c?revision=302890&view=markup
 * http://svn.php.net/viewvc/php/php-src/trunk/ext/date/lib/interval.c?revision=298973&view=markup
 *
 * @author Matt Gates <http://mgates.me>
 * @package
 */


if ( ! class_exists( '_Date_Diff' ) ) {

	class _Date_Diff
	{


		public static function _date_range_limit( $start, $end, $adj, $a, $b, $result )
		{
			if ( $result[$a] < $start ) {
				$result[$b] -= intval( ( $start - $result[$a] - 1 ) / $adj ) + 1;
				$result[$a] += $adj * intval( ( $start - $result[$a] - 1 ) / $adj + 1 );
			}

			if ( $result[$a] >= $end ) {
				$result[$b] += intval( $result[$a] / $adj );
				$result[$a] -= $adj * intval( $result[$a] / $adj );
			}

			return $result;
		}


		public static function _date_range_limit_days( $base, $result )
		{
			$days_in_month_leap = array( 31, 31, 29, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31 );
			$days_in_month = array( 31, 31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31 );

			self::_date_range_limit( 1, 13, 12, "m", "y", $base );

			$year = $base["y"];
			$month = $base["m"];

			if ( !$result["invert"] ) {
				while ( $result["d"] < 0 ) {
					$month--;
					if ( $month < 1 ) {
						$month += 12;
						$year--;
					}

					$leapyear = $year % 400 == 0 || ( $year % 100 != 0 && $year % 4 == 0 );
					$days = $leapyear ? $days_in_month_leap[$month] : $days_in_month[$month];

					$result["d"] += $days;
					$result["m"]--;
				}
			} else {
				while ( $result["d"] < 0 ) {
					$leapyear = $year % 400 == 0 || ( $year % 100 != 0 && $year % 4 == 0 );
					$days = $leapyear ? $days_in_month_leap[$month] : $days_in_month[$month];

					$result["d"] += $days;
					$result["m"]--;

					$month++;
					if ( $month > 12 ) {
						$month -= 12;
						$year++;
					}
				}
			}

			return $result;
		}


		public static function _date_normalize( $base, $result )
		{
			$result = self::_date_range_limit( 0, 60, 60, "s", "i", $result );
			$result = self::_date_range_limit( 0, 60, 60, "i", "h", $result );
			$result = self::_date_range_limit( 0, 24, 24, "h", "d", $result );
			$result = self::_date_range_limit( 0, 12, 12, "m", "y", $result );

			$result = self::_date_range_limit_days( $base, $result );

			$result = self::_date_range_limit( 0, 12, 12, "m", "y", $result );

			return $result;
		}


		public static function diff( $one, $two )
		{
			$invert = 0;
			if ( $one > $two ) {
				list( $one, $two ) = array( $two, $one );
				$invert = 1;
			}

			$key = array( "y", "m", "d", "h", "i", "s" );
			$a = array_combine( $key, array_map( "intval", explode( " ", date( "Y m d H i s", $one ) ) ) );
			$b = array_combine( $key, array_map( "intval", explode( " ", date( "Y m d H i s", $two ) ) ) );

			$result = array();
			foreach ( $a as $key => $value ) {
				$result[$key] = $b[$key] - $value;
			}

			$result["invert"] = $invert;
			$result["days"] = intval( abs( ( $one - $two )/86400 ) );

			$base = $invert ? $a : $b;
			$result = self::_date_normalize( $base, $result );

			$result['h'] = str_pad($result['h'], 2, "0", STR_PAD_LEFT);
			$result['i'] = str_pad($result['i'], 2, "0", STR_PAD_LEFT);
			$result['s'] = str_pad($result['s'], 2, "0", STR_PAD_LEFT);

			return $result;
		}


	}


}
