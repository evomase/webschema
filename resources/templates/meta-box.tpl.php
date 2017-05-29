<div id="tabs">
    <ul>
        <?php foreach ($boxes as $id => $box): ?>
            <li><a href="#<?php echo $id; ?>"><?php echo $box['title'] ?></a></li>
        <?php endforeach; ?>
    </ul>

    <?php foreach ($boxes as $id => $box): ?>
        <div id="<?php echo $id; ?>">
            <?php echo $box['content']; ?>
        </div>
    <?php endforeach; ?>
</div>