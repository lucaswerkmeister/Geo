<?php

namespace DataValues\Geo\Values;

use DataValues\DataValueObject;
use InvalidArgumentException;

/**
 * Object representing a geographic point.
 *
 * Latitude is specified in degrees within the range [-360, 360].
 * Longitude is specified in degrees within the range [-360, 360].
 *
 * @since 0.1
 *
 * @license GPL-2.0-or-later
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class LatLongValue extends DataValueObject {

	private $latitude;
	private $longitude;

	/**
	 * @param float|int $latitude Latitude in degrees within the range [-360, 360]
	 * @param float|int $longitude Longitude in degrees within the range [-360, 360]
	 *
	 * @throws InvalidArgumentException
	 */
	public function __construct( float $latitude, float $longitude ) {
		if ( is_int( $latitude ) ) {
			$latitude = (float)$latitude;
		}

		if ( is_int( $longitude ) ) {
			$longitude = (float)$longitude;
		}

		$this->assertIsLatitude( $latitude );
		$this->assertIsLongitude( $longitude );

		$this->latitude = $latitude;
		$this->longitude = $longitude;
	}

	private function assertIsLatitude( float $latitude ) {
		if ( $latitude < -360 || $latitude > 360 ) {
			throw new InvalidArgumentException( 'Latitude needs to be between -360 and 360' );
		}
	}

	private function assertIsLongitude( float $longitude ) {
		if ( $longitude < -360 || $longitude > 360 ) {
			throw new InvalidArgumentException( 'Longitude needs to be between -360 and 360' );
		}
	}

	/**
	 * @see Serializable::serialize
	 *
	 * @return string
	 */
	public function serialize(): string {
		$data = [
			$this->latitude,
			$this->longitude
		];

		return implode( '|', $data );
	}

	/**
	 * @see Serializable::unserialize
	 *
	 * @param string $value
	 *
	 * @throws InvalidArgumentException
	 */
	public function unserialize( $value ) {
		$data = explode( '|', $value, 2 );

		if ( count( $data ) < 2 ) {
			throw new InvalidArgumentException( 'Invalid serialization provided in ' . __METHOD__ );
		}

		$this->__construct( (float)$data[0], (float)$data[1] );
	}

	/**
	 * @see DataValue::getType
	 *
	 * @return string
	 */
	public static function getType(): string {
		// TODO: This really should be 'latlong' but serializations may explode if we rename it.
		return 'geocoordinate';
	}

	/**
	 * @see DataValue::getSortKey
	 *
	 * @return float
	 */
	public function getSortKey(): float {
		return $this->latitude;
	}

	/**
	 * @see DataValue::getValue
	 *
	 * @return self
	 */
	public function getValue(): self {
		return $this;
	}

	public function getLatitude(): float {
		return $this->latitude;
	}

	public function getLongitude(): float {
		return $this->longitude;
	}

	/**
	 * @see DataValue::getArrayValue
	 *
	 * @return float[]
	 */
	public function getArrayValue(): array {
		return [
			'latitude' => $this->latitude,
			'longitude' => $this->longitude
		];
	}

	/**
	 * Constructs a new instance from the provided data. Required for @see DataValueDeserializer.
	 * This is expected to round-trip with @see getArrayValue.
	 *
	 * @deprecated since 2.0.1. Static DataValue::newFromArray constructors like this are
	 *  underspecified (not in the DataValue interface), and misleadingly named (should be named
	 *  newFromArrayValue). Instead, use DataValue builder callbacks in @see DataValueDeserializer.
	 *
	 * @param mixed $data Warning! Even if this is expected to be a value as returned by
	 *  @see getArrayValue, callers of this specific newFromArray implementation can not guarantee
	 *  this. This is not even guaranteed to be an array!
	 *
	 * @throws InvalidArgumentException if $data is not in the expected format. Subclasses of
	 *  InvalidArgumentException are expected and properly handled by @see DataValueDeserializer.
	 * @return self
	 */
	public static function newFromArray( $data ): self {
		self::requireArrayFields( $data, [ 'latitude', 'longitude' ] );

		return new static( $data['latitude'], $data['longitude'] );
	}

}
