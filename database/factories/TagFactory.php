<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tag>
 */
class TagFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $techTags = [
            'Laravel', 'PHP', 'JavaScript', 'Vue.js', 'React', 'Angular', 
            'Node.js', 'Python', 'Java', 'C#', 'MySQL', 'PostgreSQL', 
            'MongoDB', 'Redis', 'Docker', 'Kubernetes', 'AWS', 'Azure', 
            'GCP', 'Git', 'GitHub', 'GitLab', 'Testing', 'TDD', 'API開発',
            'フロントエンド', 'バックエンド', 'フルスタック', 'DevOps', 
            'CI/CD', 'セキュリティ', 'パフォーマンス', 'UX/UI'
        ];
        
        return [
            'name' => $this->faker->unique()->randomElement($techTags),
        ];
    }
}