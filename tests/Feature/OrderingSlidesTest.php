<?php


namespace Dymantic\Slideshow\Tests\Feature;


use Dymantic\Slideshow\Tests\MakesModels;
use Dymantic\Slideshow\Tests\TestCase;

class OrderingSlidesTest extends TestCase
{
    use MakesModels;

    /**
     * @test
     */
    public function the_slides_can_be_ordered_by_posting_ordered_array_of_ids()
    {
        $this->disableExceptionHandling();

        $slideA = $this->createSlide();
        $slideB = $this->createSlide();
        $slideC = $this->createSlide();
        $slideD = $this->createSlide();

        $response = $this->asLoggedInUser()->json("POST", "/admin/slideshow/slide-order", [
            'ordered_ids' => [$slideB->id, $slideA->id, $slideD->id, $slideC->id]
        ]);
        $response->assertStatus(200);

        $this->assertDatabaseHas('slides', [
            'id'       => $slideA->id,
            'position' => 1
        ]);

        $this->assertDatabaseHas('slides', [
            'id'       => $slideB->id,
            'position' => 0
        ]);

        $this->assertDatabaseHas('slides', [
            'id'       => $slideC->id,
            'position' => 3
        ]);

        $this->assertDatabaseHas('slides', [
            'id'       => $slideD->id,
            'position' => 2
        ]);

    }

    /**
     *@test
     */
    public function the_ordered_ids_is_a_required_field()
    {
        $response = $this->asLoggedInUser()->json("POST", "/admin/slideshow/slide-order", []);
        $response->assertStatus(422);

        $this->assertJsonValidationErrorExists('ordered_ids', $response);
    }

    /**
     *@test
     */
    public function the_ordered_ids_must_be_an_array()
    {
        $response = $this->asLoggedInUser()->json("POST", "/admin/slideshow/slide-order", [
            'ordered_ids' => 'NOT-AN-ARRAY'
        ]);
        $response->assertStatus(422);

        $this->assertJsonValidationErrorExists('ordered_ids', $response);
    }

    /**
     *@test
     */
    public function the_ordered_ids_must_be_integers()
    {
        $response = $this->asLoggedInUser()->json("POST", "/admin/slideshow/slide-order", [
            'ordered_ids' => ['NOT', 'INTEGER', "VALUES"]
        ]);
        $response->assertStatus(422);

        $this->assertJsonValidationErrorExists('ordered_ids.0', $response);
        $this->assertJsonValidationErrorExists('ordered_ids.1', $response);
        $this->assertJsonValidationErrorExists('ordered_ids.2', $response);
    }

    /**
     *@test
     */
    public function each_of_the_ids_must_belong_to_an_existing_slide()
    {
        $slideA = $this->createSlide();
        $slideB = $this->createSlide();
        $slideC = $this->createSlide();

        $response = $this->asLoggedInUser()->json("POST", "/admin/slideshow/slide-order", [
            'ordered_ids' => [$slideA->id, 99, $slideC->id, $slideB->id]
        ]);
        $response->assertStatus(422);

        $this->assertJsonValidationErrorExists('ordered_ids.1', $response);
    }
}