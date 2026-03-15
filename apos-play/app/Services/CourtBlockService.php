<?php

namespace App\Services;

use App\Models\CourtBlock;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class CourtBlockService
{
    public function createBlock(array $data): CourtBlock
    {
        return CourtBlock::create($data);
    }

    public function deleteBlock(CourtBlock $block): void
    {
        $block->delete();
    }

    public function getBlocksForCourt(int $courtId, string $startDate, string $endDate): Collection
    {
        return CourtBlock::where('court_id', $courtId)
            ->where('start_date', '<=', $endDate)
            ->where('end_date', '>=', $startDate)
            ->get();
    }

    public function isSlotBlocked(int $courtId, string $date, string $time): bool
    {
        $blocks = CourtBlock::where('court_id', $courtId)
            ->where('start_date', '<=', $date)
            ->where('end_date', '>=', $date)
            ->get();

        foreach ($blocks as $block) {
            // Full-day block
            if (is_null($block->start_time) && is_null($block->end_time)) {
                return true;
            }

            // Time-range block
            $slotTime = Carbon::parse($time);
            $blockStart = Carbon::parse($block->start_time);
            $blockEnd = Carbon::parse($block->end_time);

            if ($slotTime >= $blockStart && $slotTime < $blockEnd) {
                return true;
            }
        }

        return false;
    }
}
