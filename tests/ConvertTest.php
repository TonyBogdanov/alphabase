<?php

namespace TonyBogdanov\Alphabase\Tests;

use PHPUnit\Framework\TestCase;
use TonyBogdanov\Alphabase\Converter;
use TonyBogdanov\Alphabase\Exception\InvalidAlphabetException;
use TonyBogdanov\Alphabase\Exception\InvalidInputException;

/**
 * Class ConvertTest
 *
 * @package TonyBogdanov\Alphabase\Tests
 */
class ConvertTest extends TestCase {

    /**
     * @return iterable
     */
    public function provider(): iterable {

        for ( $i = 1; $i < 256; $i++ ) {

            $symbols = array_map( 'chr', range( 0, $i ) );

            $input = $symbols;
            shuffle( $input );

            yield [ implode( '', $input ), implode( '', $symbols ), implode( '', array_map( 'chr', range( 0, 64 ) ) ) ];

        }

    }

    /**
     * @dataProvider provider
     *
     * @param string $input
     * @param string $inputAlphabet
     * @param string $outputAlphabet
     * @return void
     * @throws InvalidAlphabetException
     * @throws InvalidInputException
     */
    public function testConvert( string $input, string $inputAlphabet, string $outputAlphabet ): void {

        $inter = Converter::convert( $input, $inputAlphabet, $outputAlphabet );
        $final = Converter::convert( $inter, $outputAlphabet, $inputAlphabet );

        $this->assertSame( $input, $final );

    }

    /**
     * @return void
     * @throws InvalidAlphabetException
     * @throws InvalidInputException
     */
    public function testEmptyInput(): void {
        $this->assertSame( '', Converter::convert( '', 'ab', 'ab' ) );
    }

    /**
     * @return void
     * @throws InvalidAlphabetException
     * @throws InvalidInputException
     */
    public function testOnlyPaddingInput(): void {
        $this->assertSame( 'qqq', Converter::convert( 'aaa', 'ab', 'qw' ) );
    }

    /**
     * @return void
     * @throws InvalidAlphabetException
     * @throws InvalidInputException
     */
    public function testNonUniqueCharactersInAlphabet(): void {
        $this->expectException( InvalidAlphabetException::class );
        Converter::convert( 'a', 'aab', 'ab' );
    }

    /**
     * @return void
     * @throws InvalidAlphabetException
     * @throws InvalidInputException
     */
    public function testNotEnoughCharactersInAlphabet(): void {
        $this->expectException( InvalidAlphabetException::class );
        Converter::convert( 'a', 'a', 'ab' );
    }

    /**
     * @return void
     * @throws InvalidAlphabetException
     * @throws InvalidInputException
     */
    public function testCharactersOutsideOfAlphabet(): void {
        $this->expectException( InvalidInputException::class );
        Converter::convert( 'abc', 'ab', 'ab' );
    }

}
