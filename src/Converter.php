<?php

namespace TonyBogdanov\Alphabase;

use TonyBogdanov\Alphabase\Exception\InvalidAlphabetException;
use TonyBogdanov\Alphabase\Exception\InvalidInputException;

/**
 * Class Converter
 *
 * @package TonyBogdanov\Alphabase
 */
class Converter {

    /** @var array[] */
    protected static array $alphabets = [];

    /**
     * @param string $symbols
     * @return array
     * @throws InvalidAlphabetException
     */
    protected static function createAlphabet( string $symbols ): array {

        if ( ! isset( static::$alphabets[ $symbols ] ) ) {

            $alphabet = str_split( $symbols );
            $unique = array_unique( $alphabet );

            if ( count( $unique ) !== count( $alphabet ) ) {
                throw new InvalidAlphabetException( 'The alphabet must contain only unique characters.' );
            }

            if ( 2 > count( $unique ) ) {
                throw new InvalidAlphabetException( sprintf(
                    'The alphabet must contain at least 2 characters, got: %d.',
                    count( $unique ),
                ) );
            }

            static::$alphabets[ $symbols ] = [ $unique, array_flip( $unique ) ];

        }

        return static::$alphabets[ $symbols ];

    }

    /**
     * @param string $input
     * @param string $inputAlphabet
     * @param string $outputAlphabet
     * @return string
     * @throws InvalidAlphabetException
     * @throws InvalidInputException
     */
    public static function convert( string $input, string $inputAlphabet, string $outputAlphabet ): string {

        if ( '' === $input ) {
            return '';
        }

        [ $is, $isf ] = static::createAlphabet( $inputAlphabet );
        [ $os ] = static::createAlphabet( $outputAlphabet );

        $invalid = array_diff( array_unique( str_split( $input ) ), $is );
        if ( 0 < count( $invalid ) ) {
            throw new InvalidInputException( sprintf(
                'The input string contains characters outside of the input alphabet: %s.',
                json_encode( array_values( $invalid ) ),
            ) );
        }

        $ib = count( $is );
        $ob = count( $os );

        $carry = preg_match( '/^' . preg_quote( $is[0], '/' ) . '+/', $input, $match ) ? strlen( $match[0] ) : 0;
        $value = '0';

        foreach ( array_reverse( str_split( substr( $input, $carry ) ) ) as $index => $symbol ) {
            $value = bcadd( $value, bcmul( $isf[ $symbol ], bcpow( $ib, $index ) ) );
        }

        $result = [];
        while ( -1 === bccomp( 0, $value ) ) {
            array_unshift( $result, $os[ (int) bcmod( $value, $ob ) ] );
            $value = bcdiv( $value, $ob );
        }

        return ( 0 < $carry ? str_repeat( $os[0], $carry ) : '' ) . implode( '', $result );

    }

}
