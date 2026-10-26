<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="page-heading"><div><h1><?= esc($title) ?></h1><p class="subheading">Isi informasi di bawah ini dengan benar.</p></div></div>
<?php if ($errors = session()->getFlashdata('errors')): ?>
    <ul class="error-list"><?php foreach ($errors as $error): ?><li><?= esc($error) ?></li><?php endforeach ?></ul>
<?php endif ?>
<section class="panel">
    <form method="post" action="<?= site_url('data/' . $resource . ($id === null ? '' : '/' . $id)) ?>">
        <?= csrf_field() ?>
        <?php $currentGroup = null; ?>
        <?php foreach ($config['fields'] as $name => $field): ?>
            <?php
                $group = $field['group'] ?? 'Informasi';
                $value = old($name, $record[$name] ?? '');
                if ($group !== $currentGroup):
                    if ($currentGroup !== null): ?>
                        </div></fieldset>
                    <?php endif ?>
                    <fieldset class="form-section">
                        <legend><?= esc($group) ?></legend>
                        <div class="form-grid">
                    <?php $currentGroup = $group; ?>
                <?php endif ?>
                <div class="field <?= $field['type'] === 'textarea' ? 'full' : '' ?>">
                    <label for="<?= esc($name) ?>"><?= esc($field['label']) ?></label>
                    <?php if ($field['type'] === 'textarea'): ?>
                        <textarea id="<?= esc($name) ?>" name="<?= esc($name) ?>"><?= esc((string) $value) ?></textarea>
                    <?php elseif (in_array($field['type'], ['select', 'yes_no'], true)): ?>
                        <?php $choices = $field['options'] ?? ['Ya' => 'Ya', 'Tidak' => 'Tidak']; ?>
                        <select id="<?= esc($name) ?>" name="<?= esc($name) ?>" <?= str_starts_with($field['rules'], 'required') ? 'required' : '' ?>>
                            <option value="">Pilih <?= esc(strtolower($field['label'])) ?></option>
                            <?php foreach ($choices as $option => $label): ?>
                                <option value="<?= esc($option) ?>" <?= (string) $value === (string) $option ? 'selected' : '' ?>><?= esc($label) ?></option>
                            <?php endforeach ?>
                        </select>
                    <?php elseif ($field['type'] === 'classes' || $field['type'] === 'teachers'): ?>
                        <?php $items = $options[$name] ?? []; ?>
                        <select id="<?= esc($name) ?>" name="<?= esc($name) ?>">
                            <option value="">Belum ditentukan</option>
                            <?php foreach ($items as $item): ?>
                                <?php $optionValue = $field['type'] === 'classes' ? $item['id'] : $item['id']; ?>
                                <?php $optionLabel = $field['type'] === 'classes' ? $item['name'] . ' · ' . $item['academic_year'] : $item['full_name']; ?>
                                <option value="<?= esc((string) $optionValue) ?>" <?= (string) $value === (string) $optionValue ? 'selected' : '' ?>><?= esc($optionLabel) ?></option>
                            <?php endforeach ?>
                        </select>
                    <?php else: ?>
                        <input id="<?= esc($name) ?>" type="<?= esc($field['type']) ?>" name="<?= esc($name) ?>" value="<?= esc((string) $value) ?>" <?= isset($field['step']) ? 'step="' . esc($field['step']) . '"' : '' ?> <?= str_starts_with($field['rules'], 'required') ? 'required' : '' ?>>
                    <?php endif ?>
                </div>
        <?php endforeach ?>
        <?php if ($currentGroup !== null): ?></div></fieldset><?php endif ?>
        <div class="form-actions"><button class="button" type="submit">Simpan</button><a class="button secondary" href="<?= site_url('data/' . $resource) ?>">Batal</a></div>
    </form>
</section>
<?= $this->endSection() ?>
