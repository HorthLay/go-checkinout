<?php

namespace App\Livewire;

use App\Models\Mission;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class MissionAlertAdmin extends Component
{
    public $pendingMissions = [];
    public $pendingCount = 0;
    public $showDropdown = false;

    protected $listeners = ['missionStatusChanged' => 'loadPendingMissions'];

    public function mount()
    {
        $this->loadPendingMissions();
    }

    public function loadPendingMissions()
    {
        if (Auth::user() && Auth::user()->isAdmin()) {
            $this->pendingMissions = Mission::with('user')
                ->where('status', 'pending')
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
            
            $this->pendingCount = Mission::where('status', 'pending')->count();
        }
    }

    public function toggleDropdown()
    {
        $this->showDropdown = !$this->showDropdown;
    }

    public function closeDropdown()
    {
        $this->showDropdown = false;
    }

    public function render()
    {
        return view('livewire.mission-alert-admin');
    }
}